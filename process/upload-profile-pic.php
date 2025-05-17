<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Start output buffering to catch any accidental output
ob_start();

// Start session and set JSON header
session_start();
header('Content-Type: application/json');

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_pic'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Validate user session
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Upload configuration
$upload_dir = __DIR__ . '/../uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB

// Create upload directory if needed
if (!file_exists($upload_dir) && !mkdir($upload_dir, 0755, true)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Could not create upload directory']);
    exit();
}

// Process uploaded file
$file = $_FILES['profile_pic'];

// Validate file type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_types)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, or WEBP allowed']);
    exit();
}

if ($file['size'] > $max_size) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'File too large (max 2MB)']);
    exit();
}

// Generate unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
$target_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $target_path)) {
    try {
        // Delete old profile picture if exists
        $stmt = $pdo->prepare("SELECT profile_pic FROM members WHERE id = ?");
        $stmt->execute([$user_id]);
        $old_pic = $stmt->fetchColumn();
        
        if ($old_pic && file_exists($upload_dir . $old_pic)) {
            unlink($upload_dir . $old_pic);
        }

        // Update database with new filename
        $stmt = $pdo->prepare("UPDATE members SET profile_pic = ? WHERE id = ?");
        $stmt->execute([$filename, $user_id]);

        // Update session
        $_SESSION['profile_pic'] = $filename;

        ob_end_clean();
        echo json_encode(['success' => true, 'filename' => $filename]);
        exit();
    } catch (PDOException $e) {
        unlink($target_path); // Clean up uploaded file
        ob_end_clean();
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
} else {
    ob_end_clean();
    error_log("File upload failed: " . $file['error']);
    echo json_encode(['success' => false, 'message' => 'File upload failed']);
    exit();
}
?>
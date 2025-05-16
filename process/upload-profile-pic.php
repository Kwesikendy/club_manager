<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_pic'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Validate user
$user_id = (int)$_POST['user_id'];
if ($user_id !== $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Upload config
$upload_dir = __DIR__ . '/../uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB

// Create uploads directory if not exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Process file
$file = $_FILES['profile_pic'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, or WEBP allowed']);
    exit();
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File too large (max 2MB)']);
    exit();
}

// Generate unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
$target_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $target_path)) {
    // Update database
    try {
        // Delete old profile pic if exists
        $stmt = $pdo->prepare("SELECT profile_pic FROM members WHERE id = ?");
        $stmt->execute([$user_id]);
        $old_pic = $stmt->fetchColumn();
        
        if ($old_pic && file_exists($upload_dir . $old_pic)) {
            unlink($upload_dir . $old_pic);
        }

        // Update with new pic
        $stmt = $pdo->prepare("UPDATE members SET profile_pic = ? WHERE id = ?");
        $stmt->execute([$filename, $user_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        unlink($target_path); // Clean up
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Upload failed']);
}
?>
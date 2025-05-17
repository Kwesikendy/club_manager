<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Start session only if not started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

redirectIfNotLoggedIn();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];

        // Update text profile fields
        $stmt = $pdo->prepare("UPDATE members SET full_name = ?, dob = ?, course = ?, level = ?, hostel = ?, phone = ?, bio = ? WHERE id = ?");
        $stmt->execute([
            htmlspecialchars(trim($_POST['full_name'])),
            $_POST['dob'],
            htmlspecialchars(trim($_POST['course'])),
            htmlspecialchars(trim($_POST['level'])),
            htmlspecialchars(trim($_POST['hostel'])),
            htmlspecialchars(trim($_POST['phone'])),
            htmlspecialchars(trim($_POST['bio'])),
            $user_id
        ]);

        // Profile picture handling
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_pic'];

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            $max_size = 2 * 1024 * 1024;

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($mime, $allowed_types) && in_array($ext, $allowed_ext) && $file['size'] <= $max_size) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $filename = 'profile_' . $user_id . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $target_path = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $stmt = $pdo->prepare("SELECT profile_pic FROM members WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $old_pic = $stmt->fetchColumn();

                    if ($old_pic && file_exists($upload_dir . $old_pic)) {
                        unlink($upload_dir . $old_pic);
                    }

                    $stmt = $pdo->prepare("UPDATE members SET profile_pic = ? WHERE id = ?");
                    $stmt->execute([$filename, $user_id]);

                    $_SESSION['profile_pic'] = $filename;
                } else {
                    echo json_encode(['success' => false, 'message' => 'File move failed']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid image type, extension, or size']);
                exit();
            }
        }

        $_SESSION['full_name'] = htmlspecialchars(trim($_POST['full_name']));
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        exit();

    } catch (PDOException $e) {
        error_log("Profile update error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}
?>
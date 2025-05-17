<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Session validation first
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle text data
        $stmt = $pdo->prepare("
            UPDATE members SET
                full_name = ?,
                dob = ?,
                course = ?,
                level = ?,
                hostel = ?,
                phone = ?,
                bio = ?
            WHERE id = ?
        ");

        $stmt->execute([
            htmlspecialchars(trim($_POST['full_name'])),
            $_POST['dob'],
            htmlspecialchars(trim($_POST['course'])),
            htmlspecialchars(trim($_POST['level'])),
            htmlspecialchars(trim($_POST['hostel'])),
            htmlspecialchars(trim($_POST['phone'])),
            htmlspecialchars(trim($_POST['bio'])),
            $_SESSION['user_id']
        ]);

        // Handle profile picture upload if provided
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/profile_pics/';
            
            // Create directory if needed
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Validate file
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['profile_pic']['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($mime, $allowed_types) && $_FILES['profile_pic']['size'] <= $max_size) {
                // Generate unique filename
                $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                $upload_path = $upload_dir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                    // Delete old picture if exists
                    $stmt = $pdo->prepare("SELECT profile_pic FROM members WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $old_pic = $stmt->fetchColumn();
                    
                    if ($old_pic && file_exists($upload_dir . $old_pic)) {
                        unlink($upload_dir . $old_pic);
                    }
                    
                    // Update database
                    $stmt = $pdo->prepare("UPDATE members SET profile_pic = ? WHERE id = ?");
                    $stmt->execute([$filename, $_SESSION['user_id']]);
                    
                    // Update session
                    $_SESSION['profile_pic'] = $filename;
                } else {
                    header('Location: ../members/profile.php?error=upload_failed');
                    exit();
                }
            } else {
                header('Location: ../members/profile.php?error=invalid_file');
                exit();
            }
        }

        // Update session
        $_SESSION['full_name'] = htmlspecialchars(trim($_POST['full_name']));

        header('Location: ../members/profile.php?success=1');
        exit();

    } catch (PDOException $e) {
        error_log("Profile update error: " . $e->getMessage());
        header('Location: ../members/profile.php?error=database_error');
        exit();
    }
} else {
    header('Location: ../members/profile.php');
    exit();
}
?>
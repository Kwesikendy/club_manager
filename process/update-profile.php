<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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

        // Update session name if changed
        $_SESSION['full_name'] = htmlspecialchars(trim($_POST['full_name']));

        header('Location: ../members/profile.php?success=1');
        exit();

    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
} else {
    header('Location: ../members/profile.php');
    exit();
}
?>
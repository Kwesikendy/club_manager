<?php
// process/register-process.php

// 1. Start session and include config
session_start();
require_once __DIR__ . '/../includes/config.php';

// 2. Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. Sanitize and validate input
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $course = htmlspecialchars(trim($_POST['course']));
    $hostel = htmlspecialchars(trim($_POST['hostel']));
    $level = htmlspecialchars(trim($_POST['level']));
    $dob = $_POST['dob']; // Already sanitized by date input type
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $bio = htmlspecialchars(trim($_POST['bio']));

    // 4. Insert into database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO members 
            (full_name, email, course, hostel, level, dob, phone, password,bio) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$full_name, $email, $course, $hostel, $level, $dob, $phone, $password, $bio]);

        // 5. Auto-login and redirect
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['full_name'] = $full_name;
        header('Location: ../members/dashboard.php');
        exit();

    } catch (PDOException $e) {
        // 6. Handle errors
        die("Registration failed: " . $e->getMessage());
    }
} else {
    // 7. Redirect if accessed directly
    header('Location: ../members/register.php');
    exit();
}
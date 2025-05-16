<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Fetch user from database
    $stmt = $pdo->prepare("SELECT id, full_name, password FROM members WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!-- Keep your existing HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- In your header -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden w-full max-w-md">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-robot text-3xl"></i>
                    <h2 class="text-2xl font-bold">Member Login</h2>
                </div>
                <p class="text-blue-200 mt-1">Access your dashboard</p>
            </div>

            <!-- Form -->
            <form method="POST" class="p-6 space-y-4" action = "../process/login-process.php">
                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="mr-2">
                        <label for="remember" class="text-gray-700">Remember me</label>
                    </div>
                    <a href="#" class="text-blue-600 hover:underline">Forgot password?</a>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium 
                                   transition duration-300 transform hover:scale-105">
                        Login
                    </button>
                </div>

                <div class="text-center text-gray-600">
                    New member? 
                    <a href="register.php" class="text-blue-600 hover:underline">Register here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded credentials
    $adminUsername = 'admin';
    $adminPassword = '123';

    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['user_id'] = 0; 
        $_SESSION['is_admin'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login - Robotics Club</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet" />
<style>
    body {
        font-family: 'Roboto Slab', serif;
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
    }
</style>
</head>
<body class="flex items-center justify-center min-h-screen px-4">

<div class="bg-white bg-opacity-90 backdrop-blur-md rounded-xl shadow-xl max-w-md w-full p-10">
    <div class="flex flex-col items-center mb-8">
        <svg class="w-16 h-16 text-blue-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L15 12 9.75 7v10z"></path>
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"></circle>
        </svg>
        <h1 class="text-3xl font-extrabold text-gray-900">Admin</h1>
        <p class="text-gray-700 mt-1">Secure Admin Login</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-semibold">Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post"  class="space-y-6">
        <div>
            <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
            <input type="text" name="username" id="username" required
                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Enter admin username" />
        </div>

        <div>
            <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Enter password" />
        </div>

        <button type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-md transition duration-300 ease-in-out">
            Log In
        </button>
    </form>
</div>

</body>
</html>

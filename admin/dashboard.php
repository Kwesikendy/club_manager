<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/config.php';

// Fetch some stats for dashboard (adjust queries as per your db schema)
$memberCount = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
$sessionCount = $pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn();
// For payments, assuming a payments table exists
$paymentsTotal = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Robotics Club</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<style>
  /* Animate fade-in for cards */
  .fade-in {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
  }
  @keyframes fadeInUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
    from {
      opacity: 0;
      transform: translateY(20px);
    }
  }
</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-blue-700 text-white shadow-md">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    <div class="text-xl font-bold tracking-wide">Robotics Club Admin</div>
    <ul class="flex space-x-8 text-lg">
      <li><a href="dashboard.php" class="hover:text-gray-300 transition">Dashboard</a></li>
      <li><a href="members.php" class="hover:text-gray-300 transition">Members</a></li>
      <li><a href="attendance.php" class="hover:text-gray-300 transition">Attendance</a></li>
      <li><a href="payments.php" class="hover:text-gray-300 transition">Payments</a></li>
      <li><a href="logout.php" class="hover:text-gray-300 transition">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- Main Content -->
<main class="container mx-auto px-6 py-10 flex-grow">

  <h1 class="text-4xl font-extrabold mb-8 text-gray-800">Welcome, Admin!</h1>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">

    <div class="bg-white rounded-lg shadow-lg p-6 fade-in" style="animation-delay: 0.1s;">
      <h2 class="text-lg font-semibold text-gray-600 mb-3">Total Members</h2>
      <p class="text-4xl font-bold text-blue-700"><?= number_format($memberCount) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 fade-in" style="animation-delay: 0.3s;">
      <h2 class="text-lg font-semibold text-gray-600 mb-3">Attendance Sessions</h2>
      <p class="text-4xl font-bold text-blue-700"><?= number_format($sessionCount) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 fade-in" style="animation-delay: 0.5s;">
      <h2 class="text-lg font-semibold text-gray-600 mb-3">Total Payments (GHS)</h2>
      <p class="text-4xl font-bold text-blue-700">₵<?= number_format($paymentsTotal, 2) ?></p>
    </div>

  </div>

  <section class="mt-12">
    <h2 class="text-2xl font-semibold mb-6 text-gray-700">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

      <a href="members.php" class="block bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 text-center shadow-md transform transition hover:-translate-y-1">
        View Members
      </a>

      <a href="attendance.php" class="block bg-green-600 hover:bg-green-700 text-white rounded-lg p-6 text-center shadow-md transform transition hover:-translate-y-1">
        Manage Attendance
      </a>

      <a href="payments.php" class="block bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 text-center shadow-md transform transition hover:-translate-y-1">
        View Payments
      </a>

    </div>
  </section>

</main>

<!-- Footer -->
<footer class="bg-blue-700 text-white text-center py-4 mt-auto">
  <p>© <?= date('Y') ?> Robotics Club Management System. All rights reserved.</p>
</footer>

</body>
</html>

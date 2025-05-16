<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php'; // Contains redirectIfNotLoggedIn()

// This will redirect to login if no session exists
redirectIfNotLoggedIn();

// Now safely use $_SESSION['user_id'] to fetch user data
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!-- Your existing dashboard HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESA Robotics - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- In your header -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'teeter': 'teeter 1s ease-in-out infinite',
                        'zoom-in': 'zoomIn 0.5s ease-out'
                    },
                    keyframes: {
                        teeter: {
                            '0%, 100%': { transform: 'rotate(-3deg)' },
                            '50%': { transform: 'rotate(3deg)' }
                        },
                        zoomIn: {
                            '0%': { transform: 'scale(0.5)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<h1>Hello, <?= htmlspecialchars($user['full_name']) ?>!</h1>
<body class="bg-gray-100 font-sans">
    <!-- Sidebar -->
    <div class="flex h-screen">
        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-10 hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-indigo-800 text-white fixed h-full transition-all duration-300 -translate-x-full md:translate-x-0 z-20">
            <div class="p-4 flex items-center space-x-2 border-b border-indigo-700">
                <i class="fas fa-robot text-2xl text-yellow-300"></i>
                <span class="text-xl font-bold">ESA Robotics</span>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="flex items-center space-x-2 p-2 rounded hover:bg-indigo-700 transition">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="profile.php" class="flex items-center space-x-2 p-2 rounded hover:bg-indigo-700 transition">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="payment.php" class="flex items-center space-x-2 p-2 rounded hover:bg-indigo-700 transition">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Pay Dues</span>
                        </a>
                    </li>
                    <li>
                        <a href="attendance.php" class="flex items-center space-x-2 p-2 rounded hover:bg-indigo-700 transition">
                            <i class="fas fa-calendar-check"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="../process/logout.php" class="flex items-center space-x-2 p-2 rounded hover:bg-red-600 transition">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 transition-all duration-300">
            <!-- Mobile header -->
            <header class="bg-white shadow p-4 flex items-center md:hidden">
                <button id="sidebar-toggle" class="text-gray-600">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="ml-4 font-bold">Dashboard</h1>
            </header>

            <!-- Content -->
            <main class="p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Welcome Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden animate-zoom-in">
                        <div class="p-8">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-robot text-indigo-600 text-2xl animate-teeter"></i>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-800">Hello, <?= htmlspecialchars($user['full_name']) ?>!</h1>
                                    <p class="text-indigo-600">Welcome to the ESA Robotics Club Management System</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-8 py-6">
                            <p class="text-gray-600">
                                You can pay your semester dues, track your payment, get informed about events, and many others through this platform. 
                                <span class="block mt-2 font-medium text-indigo-700">Feel free to navigate as you please!</span>
                            </p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <!-- Pay Dues -->
                        <a href="payment.php" class="bg-gradient-to-r from-green-400 to-blue-500 rounded-lg shadow-md p-6 text-white transform hover:scale-105 transition duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-xl">Pay Semester Dues</h3>
                                    <p class="mt-2 opacity-90">Click to complete payment</p>
                                </div>
                                <i class="fas fa-money-bill-wave text-3xl"></i>
                            </div>
                        </a>

                        <!-- Events -->
                        <div class="bg-gradient-to-r from-purple-400 to-pink-500 rounded-lg shadow-md p-6 text-white transform hover:scale-105 transition duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-xl">Upcoming Events</h3>
                                    <p class="mt-2 opacity-90">Robotics Workshop - June 15</p>
                                </div>
                                <i class="fas fa-calendar-alt text-3xl"></i>
                            </div>
                        </div>

                        <!-- Profile -->
                        <a href="profile.php" class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg shadow-md p-6 text-white transform hover:scale-105 transition duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-xl">Complete Profile</h3>
                                    <p class="mt-2 opacity-90">Add your details</p>
                                </div>
                                <i class="fas fa-user-edit text-3xl"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('sidebar-toggle');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
    </script>
</body>
</html>
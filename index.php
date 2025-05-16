<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: members/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESA Robotics Club</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- In your header -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'typewriter': 'typewriter 2s steps(11) forwards'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        glow: {
                            '0%': { 'text-shadow': '0 0 5px #3b82f6' },
                            '100%': { 'text-shadow': '0 0 20px #3b82f6, 0 0 30px #3b82f6' }
                        },
                        typewriter: {
                            'from': { width: '0' },
                            'to': { width: '100%' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .robotics-bg {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1620712943543-bcc4688e7485?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="robotics-bg min-h-screen text-white">
    <div class="container mx-auto px-4 py-16 flex flex-col items-center justify-center min-h-screen">
        <!-- Animated Header -->
        <div class="text-center mb-12 overflow-hidden">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 inline-block">
                <span class="typewriter block whitespace-nowrap">ESA ROBOTICS</span>
            </h1>
            <p class="text-xl md:text-2xl mt-4 animate-glow">
                Engineering the Future, One Circuit at a Time
            </p>
        </div>

        <!-- Animated Robot Icon -->
        <div class="mb-16 animate-float">
            <i class="fas fa-robot text-8xl text-blue-400"></i>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-2xl">
            <!-- Member Button -->
            <a href="members/register.php" 
               class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 
                      rounded-xl p-8 text-center shadow-2xl transform hover:scale-105 transition-all duration-300 
                      border-2 border-blue-400 hover:border-white">
                <div class="flex flex-col items-center">
                    <i class="fas fa-user-astronaut text-5xl mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">MEMBER PORTAL</h3>
                    <p class="text-blue-200">Join our robotics community</p>
                </div>
            </a>

            <!-- Admin Button -->
            <a href="admin/login.php" 
               class="bg-gradient-to-r from-purple-600 to-pink-700 hover:from-purple-500 hover:to-pink-600 
                      rounded-xl p-8 text-center shadow-2xl transform hover:scale-105 transition-all duration-300 
                      border-2 border-purple-400 hover:border-white">
                <div class="flex flex-col items-center">
                    <i class="fas fa-user-shield text-5xl mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">ADMIN PORTAL</h3>
                    <p class="text-purple-200">Manage club operations</p>
                </div>
            </a>
        </div>

        <!-- Footer Note -->
        <p class="mt-16 text-center text-gray-300">
            <i class="fas fa-microchip"></i> Powered by ESA Engineering Students Association
        </p>
    </div>
</body>
</html>
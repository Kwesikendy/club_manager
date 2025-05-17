<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validate input (simplified example)
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $course = $_POST['course'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // 2. Insert into database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO members (full_name, email, course, password) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$full_name, $email, $course, $password]);

        // 3. Auto-login after registration
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['full_name'] = $full_name;
        header('Location: dashboard.php');
        exit();

    } catch (PDOException $e) {
        $error = "Email already exists!";
    }
}
?>

<!-- Keep your existing HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- In your header -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden w-full max-w-md">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-robot text-3xl"></i>
                    <h2 class="text-2xl font-bold">Member Registration</h2>
                </div>
                <p class="text-blue-200 mt-1">Join ESA Robotics Club</p>
            </div>

            <!-- Form -->
            <form method="POST" class="p-6 space-y-4" action = "../process/register-process.php">
                <!-- Error message -->
                 
                <div>
                    <label class="block text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="full_name" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Course</label>
                    <select name="course" class="w-full px-4 py-2 border rounded-lg">
                        <option value="Computer Science">Computer Science</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                        <option value = "Computer Egineering">Computer Engineering</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <!-- This part is for the date of birth section -->
                <div>
                    <label class="block text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="dob" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <!-- This part is the phone number section -->
                <div>
                    <label class="block text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <!-- this part is the hostel section where the user tells us the hostel they live in currently but it's optional so if they don't stay in hostels, they can just ignore it -->
                 <div>
                    <label class="block text-gray-700 mb-1">Hostel</label>
                    <input type="text" name="hostel" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- This part is the year or level the member is in so it's going to be a seelction thing with the options 100, 200, 300 and 400 -->
                <div>
                    <label class="block text-gray-700 mb-1">Level</label>
                    <select name="level" class="w-full px-4 py-2 border rounded-lg">
                        <option value="100">100 Level</option>
                        <option value="200">200 Level</option>
                        <option value="300">300 Level</option>
                        <option value="400">400 Level</option>
                    </select>
                    <div>
    <label class="block text-gray-700 mb-1">Bio (Tell us about yourself)</label>
    <textarea name="bio" rows="3" 
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="Your interests, skills, etc."></textarea>
</div>
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium 
                                   transition duration-300 transform hover:scale-105">
                        Register Now
                    </button>
                </div>

                <div class="text-center text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-blue-600 hover:underline">Login here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
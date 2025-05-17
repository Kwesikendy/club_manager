<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';
redirectIfNotLoggedIn();

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ESA Robotics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto p-4 md:p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <img src="<?= !empty($user['profile_pic']) ? '../uploads/'.$user['profile_pic'] : '../assets/images/default-avatar.webp' ?>" 
                             alt="Profile" 
                             class="w-20 h-20 rounded-full border-4 border-white/30 object-cover">
                        <button id="uploadTrigger" class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold"><?= htmlspecialchars($user['full_name']) ?></h1>
                        <p class="text-blue-200"><?= htmlspecialchars($user['course']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <form id="profileForm" action="../process/update-profile.php" method="POST" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hidden File Input -->
                <input type="file" id="profileUpload" name="profile_pic" accept="image/*" class="hidden">

                <!-- Personal Info -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">Personal Information</h2>
                    
                    <div>
                        <label class="block text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Academic Info -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">Academic Details</h2>
                    
                    <div>
                        <label class="block text-gray-700 mb-1">Course</label>
                        <select name="course" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Computer Science" <?= $user['course'] == 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                            <option value="Electrical Engineering" <?= $user['course'] == 'Electrical Engineering' ? 'selected' : '' ?>>Electrical Engineering</option>
                            <option value="Mechanical Engineering" <?= $user['course'] == 'Mechanical Engineering' ? 'selected' : '' ?>>Mechanical Engineering</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Level</label>
                        <select name="level" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="100L" <?= $user['level'] == '100L' ? 'selected' : '' ?>>100 Level</option>
                            <option value="200L" <?= $user['level'] == '200L' ? 'selected' : '' ?>>200 Level</option>
                            <option value="300L" <?= $user['level'] == '300L' ? 'selected' : '' ?>>300 Level</option>
                            <option value="400L" <?= $user['level'] == '400L' ? 'selected' : '' ?>>400 Level</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Hostel</label>
                        <input type="text" name="hostel" value="<?= htmlspecialchars($user['hostel']) ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="md:col-span-2 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">Contact Information</h2>
                    
                    <div>
                        <label class="block text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Bio</label>
                        <textarea name="bio" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($user['bio']) ?></textarea>
                        <!-- Add the bio information to this section of the php script but then in the user login forum, add another part which will take the user's 
                         bio and then configure it to store it in the database -->
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-2 pt-4">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 flex items-center justify-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Picture Upload Script -->
   <script>
// Handle profile form submission via AJAX
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent normal form submission

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            // Redirect after 1.5 seconds to dashboard page
            setTimeout(() => {
                window.location.href = 'dashboard.php'; // adjust path if needed
            }, 1500);
        }
    })
    .catch(() => {
        alert('An error occurred. Please try again.');
    });
});

// Optional: If you have a profile picture upload trigger/button
const uploadTrigger = document.getElementById('uploadTrigger');
const profileUploadInput = document.getElementById('profileUpload');

if (uploadTrigger && profileUploadInput) {
    uploadTrigger.addEventListener('click', (e) => {
        e.preventDefault();
        profileUploadInput.click();
    });

    profileUploadInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const formData = new FormData();
            formData.append('profile_pic', this.files[0]);
            // If you need to send user_id or other data, append here:
            // formData.append('user_id', '<?= $_SESSION['user_id'] ?>');

            fetch('../process/upload-profile-pic.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload to reflect new profile picture
                    window.location.reload();
                } else {
                    alert(data.message || 'Upload failed');
                }
            })
            .catch(() => {
                alert('An error occurred during upload.');
            });
        }
    });
}
</script>


    
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Only admin can access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../admin/login.php');
    exit();
}

// Fetch all members except admins
$stmt = $pdo->query("SELECT id, full_name, email, course, hostel, level, dob, phone, created_at, profile_pic FROM members WHERE is_admin = 0 ORDER BY full_name ASC");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin - View Members</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-7xl mx-auto bg-white shadow rounded-lg p-6">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Club Members</h1>

    <?php if (empty($members)): ?>
        <p class="text-center text-gray-600">No members found.</p>
    <?php else: ?>
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-blue-600 text-white">
                <th class="p-3 border border-blue-700">Profile</th>
                <th class="p-3 border border-blue-700">Name</th>
                <th class="p-3 border border-blue-700">Email</th>
                <th class="p-3 border border-blue-700">Course</th>
                <th class="p-3 border border-blue-700">Hostel</th>
                <th class="p-3 border border-blue-700">Level</th>
                <th class="p-3 border border-blue-700">DOB</th>
                <th class="p-3 border border-blue-700">Phone</th>
                <th class="p-3 border border-blue-700">Joined</th>
                <th class="p-3 border border-blue-700">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
                <tr class="text-center hover:bg-blue-50">
                    <td class="p-2 border border-gray-300">
                        <?php if (!empty($m['profile_pic'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($m['profile_pic']) ?>" alt="Profile Pic" class="mx-auto w-12 h-12 rounded-full object-cover" />
                        <?php else: ?>
                            <div class="mx-auto w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">N/A</div>
                        <?php endif; ?>
                    </td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['full_name']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['email']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['course']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['hostel']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['level']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['dob']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['phone']) ?></td>
                    <td class="p-2 border border-gray-300"><?= htmlspecialchars($m['created_at']) ?></td>
                    <td class="p-2 border border-gray-300">
                        <a href="member_details.php?id=<?= $m['id'] ?>" 
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</body>
</html>
                            
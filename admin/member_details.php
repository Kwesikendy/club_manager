<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Only admin can access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../admin/login.php');
    exit();
}

// Validate and get member ID from GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid member ID.');
}

$memberId = (int) $_GET['id'];

// Fetch member data
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = :id AND is_admin = 0");
$stmt->execute(['id' => $memberId]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    die('Member not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Member Details - <?= htmlspecialchars($member['full_name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-8">
    <a href="members.php" class="inline-block mb-6 text-blue-600 hover:underline">&larr; Back to Members</a>

    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
        <div class="flex-shrink-0">
            <?php if (!empty($member['profile_pic'])): ?>
                <img src="../uploads/<?= htmlspecialchars($member['profile_pic']) ?>" alt="Profile Picture" class="w-40 h-40 rounded-full object-cover shadow" />
            <?php else: ?>
                <div class="w-40 h-40 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xl font-semibold">
                    No Image
                </div>
            <?php endif; ?>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-bold mb-2 text-gray-800"><?= htmlspecialchars($member['full_name']) ?></h1>
            <p class="text-gray-600 mb-4 italic"><?= htmlspecialchars($member['bio'] ?? 'No bio available.') ?></p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div><strong>Email:</strong> <?= htmlspecialchars($member['email']) ?></div>
                <div><strong>Course:</strong> <?= htmlspecialchars($member['course']) ?></div>
                <div><strong>Hostel:</strong> <?= htmlspecialchars($member['hostel']) ?></div>
                <div><strong>Level:</strong> <?= htmlspecialchars($member['level']) ?></div>
                <div><strong>Date of Birth:</strong> <?= htmlspecialchars($member['dob']) ?></div>
                <div><strong>Phone:</strong> <?= htmlspecialchars($member['phone']) ?></div>
                <div><strong>Joined On:</strong> <?= htmlspecialchars($member['created_at']) ?></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

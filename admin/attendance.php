<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_date = $_POST['session_date'] ?? '';
    $venue = trim($_POST['venue'] ?? '');

    if ($session_date && $venue) {
        $stmt = $pdo->prepare("INSERT INTO sessions (session_date, venue, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$session_date, $venue, $_SESSION['user_id']]);
        $success = "Session created successfully.";
    } else {
        $error = "Please fill in all fields.";
    }
}

$sessions = $pdo->query("SELECT * FROM sessions ORDER BY session_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin - Attendance Sessions</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<style>
    /* Smooth hover on table rows */
    tbody tr:hover {
        background-color: #eff6ff; /* Tailwind blue-100 */
    }
</style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen flex items-center justify-center p-6">

<div class="max-w-5xl w-full bg-white rounded-xl shadow-lg p-8">

    <h1 class="text-4xl font-extrabold text-blue-700 mb-8 text-center">Manage Attendance Sessions</h1>

    <?php if ($success): ?>
        <div class="flex items-center gap-3 bg-green-100 border border-green-400 text-green-700 px-5 py-3 rounded mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="flex items-center gap-3 bg-red-100 border border-red-400 text-red-700 px-5 py-3 rounded mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="post" class="mb-10 bg-blue-50 p-6 rounded-lg shadow-inner">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="session_date" class="block mb-2 font-semibold text-blue-700">Session Date</label>
                <input
                    type="date" id="session_date" name="session_date" required
                    class="w-full rounded border border-blue-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
            </div>
            <div>
                <label for="venue" class="block mb-2 font-semibold text-blue-700">Venue</label>
                <input
                    type="text" id="venue" name="venue" required
                    placeholder="Enter venue"
                    class="w-full rounded border border-blue-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
            </div>
        </div>
        <button
            type="submit"
            class="mt-6 w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded shadow-lg transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-400"
        >
            Create Session
        </button>
    </form>

    <h2 class="text-3xl font-semibold mb-6 text-blue-700 border-b border-blue-200 pb-2">Existing Sessions</h2>

    <div class="overflow-x-auto rounded shadow">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-blue-100 text-blue-900 uppercase text-sm font-bold tracking-wider">
                    <th class="p-4 text-left">Date</th>
                    <th class="p-4 text-left">Venue</th>
                    <th class="p-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sessions) === 0): ?>
                    <tr>
                        <td colspan="3" class="p-6 text-center text-gray-500 italic">No sessions found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sessions as $session): ?>
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition">
                            <td class="p-4"><?= htmlspecialchars(date('F j, Y', strtotime($session['session_date']))) ?></td>
                            <td class="p-4"><?= htmlspecialchars($session['venue']) ?></td>
                            <td class="p-4 text-center">
                                <a
                                  href="mark_attendance.php?session_id=<?= $session['id'] ?>"
                                  class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded transition"
                                >
                                    Mark Attendance
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

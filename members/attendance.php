<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// Fetch all sessions ordered by date
$sessions = $pdo->query("SELECT id, session_date, venue FROM sessions ORDER BY session_date DESC")->fetchAll();

// Fetch this user's attendance records keyed by session_id
$stmt = $pdo->prepare("SELECT session_id, status FROM attendance_records WHERE member_id = ?");
$stmt->execute([$user_id]);
$attendance_records = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your Attendance History</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<!-- Navbar -->
<header class="bg-white shadow-md">
  <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Attendance History</h1>
    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 font-semibold transition">
      &larr; Back to Dashboard
    </a>
  </div>
</header>

<!-- Main content -->
<main class="max-w-6xl mx-auto p-6">
  <section class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-3xl font-semibold text-gray-900 mb-8">Your Attendance Records</h2>

    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden shadow-sm">
      <thead class="bg-blue-600 text-white uppercase text-sm font-semibold tracking-wide">
        <tr>
          <th class="py-3 px-6 text-left">Date</th>
          <th class="py-3 px-6 text-left">Venue</th>
          <th class="py-3 px-6 text-center">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($sessions)): ?>
          <tr>
            <td colspan="3" class="p-6 text-center text-gray-500 italic">No sessions found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($sessions as $session):
            $status = $attendance_records[$session['id']] ?? 'not_marked';
            $status_label = match($status) {
                'present' => 'Present',
                'absent' => 'Absent',
                default => 'Not Marked',
            };
            $status_color = match($status) {
                'present' => 'bg-green-100 text-green-700',
                'absent' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-500',
            };
          ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="py-4 px-6 text-gray-700 font-medium"><?= htmlspecialchars($session['session_date']) ?></td>
            <td class="py-4 px-6 text-gray-700"><?= htmlspecialchars($session['venue']) ?></td>
            <td class="py-4 px-6 text-center">
              <span class="inline-block px-4 py-1 rounded-full font-semibold <?= $status_color ?>">
                <?= $status_label ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>

</body>
</html>

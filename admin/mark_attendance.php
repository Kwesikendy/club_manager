<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    header('Location: attendance.php');
    exit;
}

// Fetch session info
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ?");
$stmt->execute([$session_id]);
$session = $stmt->fetch();
if (!$session) {
    die('Session not found.');
}

// Fetch all members
$members = $pdo->query("SELECT id, full_name, email FROM members ORDER BY full_name")->fetchAll();

// Fetch existing attendance records for this session
$attendanceStmt = $pdo->prepare("SELECT member_id, status FROM attendance_records WHERE session_id = ?");
$attendanceStmt->execute([$session_id]);
$attendanceRecords = $attendanceStmt->fetchAll(PDO::FETCH_KEY_PAIR); // member_id => status
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Mark Attendance for Session on <?= htmlspecialchars($session['session_date']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<style>
  /* Custom scrollbar for better UX */
  ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
  }
  ::-webkit-scrollbar-thumb {
      background-color: #4f46e5; /* Indigo-600 */
      border-radius: 10px;
  }
  ::-webkit-scrollbar-track {
      background: #e0e7ff; /* Indigo-100 */
  }
</style>
</head>
<body class="bg-indigo-50 min-h-screen flex flex-col">

<div class="container mx-auto px-4 py-10 flex-grow">

    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-8">

        <h1 class="text-4xl font-extrabold text-indigo-700 mb-4 tracking-wide">
            Mark Attendance
        </h1>
        <p class="text-gray-700 mb-8 text-lg">
            Session Date: <span class="font-semibold"><?= htmlspecialchars($session['session_date']) ?></span> <br />
            Venue: <span class="font-semibold"><?= htmlspecialchars($session['venue']) ?></span>
        </p>

        <a href="attendance.php"
           class="inline-block mb-6 text-indigo-600 font-semibold hover:text-indigo-800 transition duration-200">
           &larr; Back to Sessions
        </a>

        <div class="overflow-x-auto rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-indigo-200">
                <thead class="bg-indigo-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                            Member Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider">
                            Present
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider">
                            Absent
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-indigo-200">
                    <?php foreach ($members as $member):
                        $status = $attendanceRecords[$member['id']] ?? '';
                    ?>
                        <tr class="hover:bg-indigo-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-semibold"><?= htmlspecialchars($member['full_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-700 text-sm"><?= htmlspecialchars($member['email']) ?></td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button
                                    class="attendance-button px-4 py-2 rounded-lg border-2 border-green-600 hover:bg-green-600 hover:text-white transition duration-300 font-semibold <?= $status === 'present' ? 'bg-green-600 text-white' : 'text-green-600' ?>"
                                    data-session-id="<?= $session_id ?>"
                                    data-member-id="<?= $member['id'] ?>"
                                    data-status="present"
                                    type="button"
                                    aria-label="Mark <?= htmlspecialchars($member['full_name']) ?> as Present"
                                >Present</button>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button
                                    class="attendance-button px-4 py-2 rounded-lg border-2 border-red-600 hover:bg-red-600 hover:text-white transition duration-300 font-semibold <?= $status === 'absent' ? 'bg-red-600 text-white' : 'text-red-600' ?>"
                                    data-session-id="<?= $session_id ?>"
                                    data-member-id="<?= $member['id'] ?>"
                                    data-status="absent"
                                    type="button"
                                    aria-label="Mark <?= htmlspecialchars($member['full_name']) ?> as Absent"
                                >Absent</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="mt-6 text-right">
    <a href="dashboard.php" 
       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded transition">
       Finish
    </a>
</div>

        </div>

    </div>

</div>

<footer class="bg-indigo-700 text-indigo-100 py-4 text-center select-none">
    &copy; <?= date('Y') ?> Club Management System
</footer>

<script>
document.querySelectorAll('.attendance-button').forEach(button => {
    button.addEventListener('click', () => {
        const sessionId = button.dataset.sessionId;
        const memberId = button.dataset.memberId;
        const status = button.dataset.status;

        fetch('process_attendance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ session_id: sessionId, member_id: memberId, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const parentRow = button.closest('tr');
                parentRow.querySelectorAll('.attendance-button').forEach(btn => {
                    btn.classList.remove('bg-green-600', 'bg-red-600', 'text-white', 'text-green-600', 'text-red-600');
                    btn.classList.add(btn.dataset.status === 'present' ? 'text-green-600' : 'text-red-600');
                });
                if (status === 'present') {
                    button.classList.add('bg-green-600', 'text-white');
                    button.classList.remove('text-green-600');
                } else {
                    button.classList.add('bg-red-600', 'text-white');
                    button.classList.remove('text-red-600');
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Network error.'));
    });
});
</script>

</body>
</html>

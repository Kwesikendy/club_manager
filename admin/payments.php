<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

$sql = "SELECT p.id, p.reference, p.amount, p.status, p.paid_at, m.full_name, m.email
        FROM payments p
        LEFT JOIN members m ON p.user_id = m.id
        ORDER BY p.paid_at DESC";

$payments = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin - Payments</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-indigo-100 min-h-screen p-8">

<div class="max-w-7xl mx-auto">

    <h1 class="text-4xl font-extrabold mb-10 text-indigo-900 drop-shadow-lg">
        💰 Payments Overview
    </h1>

    <?php if (empty($payments)): ?>
        <div class="bg-white p-6 rounded-lg shadow-md text-center text-gray-500 text-lg">
            No payments found.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-lg shadow-lg bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase tracking-wider">Reference</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase tracking-wider">Member Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-indigo-900 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-indigo-900 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-indigo-900 uppercase tracking-wider">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-indigo-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-indigo-900 text-sm"><?= htmlspecialchars($payment['reference']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-800 text-sm"><?= htmlspecialchars($payment['full_name'] ?? 'Unknown') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-700 text-sm"><?= htmlspecialchars($payment['email'] ?? 'Unknown') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-indigo-900 text-sm">GH₵<?= number_format($payment['amount'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <?php if ($payment['status'] === 'completed'): ?>
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold tracking-wide">Completed</span>
                                <?php elseif ($payment['status'] === 'pending'): ?>
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-semibold tracking-wide">Pending</span>
                                <?php else: ?>
                                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold tracking-wide"><?= htmlspecialchars(ucfirst($payment['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-indigo-600 text-sm font-mono">
                                <?= htmlspecialchars(date('Y-m-d H:i', strtotime($payment['paid_at']))) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-10 text-center">
        <a href="dashboard.php" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-indigo-700 transition font-semibold">
            ← Back to Dashboard
        </a>
    </div>

</div>

</body>
</html>

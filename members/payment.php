<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// For now, fixed dues amount in Ghana Cedis - you can change or fetch from DB later
$dues = 40; // GH₵500

// Get user email from DB (required for Paystack)
$stmt = $pdo->prepare("SELECT email FROM members WHERE id = ?");
$stmt->execute([$user_id]);
$user_email = $stmt->fetchColumn();

if (!$user_email) {
    // fallback email if none found in DB (shouldn't happen ideally)
    $user_email = 'user@example.com';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pay Dues - ESA Robotics</title>
<script src="https://js.paystack.co/v1/inline.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded shadow max-w-md w-full text-center">
    <h1 class="text-2xl font-bold mb-4">Pay Your Semester Dues</h1>
    <p class="mb-6 text-lg">Amount to Pay: <strong>GH₵<?= number_format($dues) ?></strong></p>

    <button
        id="payButton"
        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-semibold transition"
    >
        Pay Now
    </button>
</div>

<script>
const payButton = document.getElementById('payButton');

payButton.addEventListener('click', () => {
    let handler = PaystackPop.setup({
        key: 'pk_test_eccf741fd04a23169668e18015107019e40cf7b2', // Replace this with your Paystack public key
        email: '<?= htmlspecialchars($user_email) ?>',
        amount: <?= $dues * 100 ?>, // Paystack amount in pesewas (multiply by 100)
        currency: 'GHS',
        ref: 'esa-<?= $user_id ?>-' + Math.floor((Math.random() * 1000000000) + 1), // unique ref

        callback: function(response) {
            alert('Payment successful! Reference: ' + response.reference);
            window.location.href = 'dashboard.php'; // redirect to dashboard after success
        },

        onClose: function() {
            alert('Payment cancelled. You can try again.');
        }
    });

    handler.openIframe();
});
</script>

</body>
</html>

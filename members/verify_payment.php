<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

redirectIfNotLoggedIn();

if (!isset($_GET['reference'])) {
    die("No payment reference provided.");
}

$reference = $_GET['reference'];
$user_id = $_SESSION['user_id'];

// Use your Paystack SECRET key here (sk_...)
// Never expose this key publicly
$secret_key = 'sk_test_5fe80ae22648633724269018f897d5b12f738253'; // <-- Replace with your actual secret key

// Initialize cURL
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $secret_key",
        "Cache-Control: no-cache"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error #: " . $err);
}

$result = json_decode($response, true);

if (!$result['status']) {
    die("Payment verification failed: " . htmlspecialchars($result['message']));
}

$data = $result['data'];

// Check if payment was successful
if ($data['status'] === 'success') {
    // Save payment info in database (e.g., payments table)
    $stmt = $pdo->prepare("INSERT INTO payments (user_id, reference, amount, status, paid_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $user_id,
        $data['reference'],
        $data['amount'] / 100, // Convert pesewas to cedis
        $data['status']
    ]);

    echo "<h1>Payment successful!</h1>";
    echo "<p>Reference: " . htmlspecialchars($data['reference']) . "</p>";
    echo "<p>Amount paid: GH₵" . number_format($data['amount'] / 100, 2) . "</p>";
    echo '<p><a href="dashboard.php">Return to Dashboard</a></p>';

} else {
    echo "<h1>Payment not successful.</h1>";
    echo '<p><a href="payment.php">Try again</a></p>';
}
?>

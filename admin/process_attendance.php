<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

redirectIfNotLoggedIn();

if (!isAdmin()) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$session_id = $data['session_id'] ?? null;
$member_id = $data['member_id'] ?? null;
$status = $data['status'] ?? null;

if (!$session_id || !$member_id || !in_array($status, ['present', 'absent'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

try {
    // Check if attendance record exists
    $stmt = $pdo->prepare("SELECT id FROM attendance_records WHERE session_id = ? AND member_id = ?");
    $stmt->execute([$session_id, $member_id]);
    $record = $stmt->fetch();

    if ($record) {
        // Update existing record
        $update = $pdo->prepare("UPDATE attendance_records SET status = ? WHERE id = ?");
        $update->execute([$status, $record['id']]);
    } else {
        // Insert new record
        $insert = $pdo->prepare("INSERT INTO attendance_records (session_id, member_id, status) VALUES (?, ?, ?)");
        $insert->execute([$session_id, $member_id, $status]);
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Attendance updated']);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}

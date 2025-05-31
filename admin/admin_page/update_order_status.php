<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');
include "../koneksi.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['order_id'], $input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$order_id = (int)$input['order_id'];
$status = $conn->real_escape_string($input['status']);

// Validasi status
$allowed_statuses = ['pending', 'diproses', 'selesai'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Update status
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
$stmt->close();
$conn->close();
?>
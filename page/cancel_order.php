<?php
session_start();
include "../config.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_POST['order_id'];

// Periksa apakah pesanan milik user dan status masih pending/diproses
$sql = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = $result->fetch_assoc();

if ($order['status'] === 'diproses') {
    echo json_encode([
        'success' => false, 
        'message' => 'Pesanan #'.$order_id.' sedang diproses. Untuk pembatalan, silahkan hubungi admin via WhatsApp: 081234567890 atau email: admin@dapuraizlan.com'
    ]);
    exit();
}

if ($order['status'] !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Hanya pesanan dengan status menunggu yang bisa dibatalkan']);
    exit();
}

// Update status pesanan
$sql = "UPDATE orders SET status = 'dibatalkan', tanggal_dibatalkan = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    // Log the cancellation
    $log_sql = "INSERT INTO order_logs (order_id, action, description, created_at) 
                VALUES (?, 'cancel', 'Pesanan dibatalkan oleh pelanggan', NOW())";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("i", $order_id);
    $log_stmt->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
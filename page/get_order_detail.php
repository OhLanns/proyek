<?php
include "../config.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

// Get order info
$sql = "SELECT id, tanggal, total, payment_method, status, payment_proof 
        FROM orders 
        WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = $result->fetch_assoc();

// Get order items
$sql = "SELECT m.judul, oi.quantity, oi.price 
        FROM order_items oi 
        JOIN menu m ON oi.menu_id = m.id 
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    'success' => true,
    'order' => $order,
    'items' => $items
]);

$conn->close();
?>
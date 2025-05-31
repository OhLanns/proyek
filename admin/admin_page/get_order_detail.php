<?php
include "../koneksi.php";

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

$orderId = $_GET['order_id'];

// Fetch order details with payment proof
$orderSql = "SELECT o.*, u.nama as customer_name, u.no_telepon as customer_phone, u.alamat as user_address
             FROM orders o
             JOIN users u ON o.user_id = u.id
             WHERE o.id = ?";
$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

if ($orderResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = $orderResult->fetch_assoc();

// Fetch order items
$itemsSql = "SELECT oi.*, m.judul, m.img as menu_image
             FROM order_items oi
             JOIN menu m ON oi.menu_id = m.id
             WHERE oi.order_id = ?";
$itemsStmt = $conn->prepare($itemsSql);
$itemsStmt->bind_param("i", $orderId);
$itemsStmt->execute();
$itemsResult = $itemsStmt->get_result();
$items = $itemsResult->fetch_all(MYSQLI_ASSOC);

// Add debug info for payment proof
$paymentProofPath = '/gambar/payment/' . $order['payment_proof'];
$order['payment_proof_exists'] = file_exists($paymentProofPath) && is_file($paymentProofPath);

// Tambahkan ini sebelum mengembalikan response
error_log("Payment proof path: " . $paymentProofPath);
error_log("File exists: " . (file_exists($paymentProofPath) ? 'Yes' : 'No'));
// Prepare response
$response = [
    'success' => true,
    'order' => $order,
    'items' => $items,
    'debug' => [
        'payment_proof_path' => $paymentProofPath,
        'absolute_path' => realpath($paymentProofPath)
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>
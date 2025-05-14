<?php
include "../config.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$menu_id = intval($data['menu_id']);
$quantity = intval($data['quantity']);
$user_id = $_SESSION['user_id'];

// Check if item already exists in cart
$sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND menu_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $menu_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing item
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_quantity, $row['id']);
} else {
    // Add new item
    $sql = "INSERT INTO cart (user_id, menu_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $menu_id, $quantity);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
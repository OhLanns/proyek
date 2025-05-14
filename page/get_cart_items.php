<?php
include "../config.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT m.id, m.judul, m.img, m.harga, c.quantity 
        FROM cart c 
        JOIN menu m ON c.menu_id = m.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    'success' => true,
    'items' => $items
]);

$stmt->close();
$conn->close();
?>
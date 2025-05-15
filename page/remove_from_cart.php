<?php
include "../config.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$menu_id = intval($data['menu_id']);
$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM cart WHERE user_id = ? AND menu_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $menu_id);
$success = $stmt->execute();

echo json_encode(['success' => $success]);

$stmt->close();
$conn->close();
?>
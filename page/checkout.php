<?php
include "../config.php";

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Start transaction
$conn->begin_transaction();

try {
    // 1. Get cart items
    $sql = "SELECT m.id, m.harga, c.quantity 
            FROM cart c 
            JOIN menu m ON c.menu_id = m.id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Keranjang kosong");
    }
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total += $row['harga'] * $row['quantity'];
    }
    
    // 2. Create order record
    $payment_method = $_POST['payment_method'] ?? 'Dana';
    $payment_proof = '';
    
    // Handle file upload
    if (isset($_FILES['payment_proof'])) {
        $target_dir = "../gambar/payment/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $new_filename = "proof_" . $user_id . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
            $payment_proof = $new_filename;
        }
    }
    
    $sql = "INSERT INTO orders (user_id, total, status, payment_method, payment_proof) 
            VALUES (?, ?, 'pending', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $user_id, $total, $payment_method, $payment_proof);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    // 3. Create order items
    foreach ($items as $item) {
        $sql = "INSERT INTO order_items (order_id, menu_id, quantity, price) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['harga']);
        $stmt->execute();
    }
    
    // 4. Clear cart
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
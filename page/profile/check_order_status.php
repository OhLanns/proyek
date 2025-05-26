<?php
// Jika request AJAX untuk pengecekan real-time
if(isset($_GET['ajax_check'])) {
    header('Content-Type: application/json');
    
    $response = [
        'hasActiveOrders' => false,
        'message' => '',
        'cart_count' => 0,
        'active_orders' => 0
    ];

    if(isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // 1. Cek keranjang belanja
        $cart_stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $response['cart_count'] = $cart_stmt->get_result()->fetch_row()[0];
        $cart_stmt->close();
        
        // 2. Cek pesanan aktif
        $order_stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status IN ('pending', 'processing')");
        $order_stmt->bind_param("i", $user_id);
        $order_stmt->execute();
        $response['active_orders'] = $order_stmt->get_result()->fetch_row()[0];
        $order_stmt->close();
        
        $response['hasActiveOrders'] = ($response['cart_count'] > 0 || $response['active_orders'] > 0);
        $response['message'] = $response['hasActiveOrders'] 
            ? "Anda memiliki {$response['cart_count']} item dikeranjang dan {$response['active_orders']} pesanan aktif" 
            : "Tidak ada pesanan aktif";
    }
    
    echo json_encode($response);
    exit();
}

// Jika akses langsung ke file (untuk include di halaman lain)
function checkActiveOrders($user_id, $conn) {
    // 1. Cek keranjang
    $cart_stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_count = $cart_stmt->get_result()->fetch_row()[0];
    $cart_stmt->close();
    
    // 2. Cek pesanan aktif
    $order_stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status IN ('pending', 'processing')");
    $order_stmt->bind_param("i", $user_id);
    $order_stmt->execute();
    $order_count = $order_stmt->get_result()->fetch_row()[0];
    $order_stmt->close();
    
    return ($cart_count > 0 || $order_count > 0);
}

// Jika file ini diinclude dari halaman lain, kembalikan fungsi
return function($user_id) use ($conn) {
    return checkActiveOrders($user_id, $conn);
};
?>
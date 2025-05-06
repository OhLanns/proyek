<?php
include "../config.php";

// Pastikan tidak ada output sebelum header
if (ob_get_level()) ob_end_clean();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi dasar
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Harap isi semua field";
        header("Location: ../index.php?halaman=login");
        exit();
    }

    // Query dengan parameterized statement
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
        die("Error prepare statement: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        die("Error execute: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Login sukses
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // TAMBAHKAN BAGIAN INI UNTUK REDIRECT
            if (isset($_SESSION['redirect_url'])) {
                $redirect_url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
                header("Location:../" . $redirect_url);
            } else {
                header("Location: ../index.php?halaman=home");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Kombinasi username/password salah";
        }
    } else {
        $_SESSION['login_error'] = "User tidak ditemukan";
    }
    
    $stmt->close();
}

// Jika gagal, kembali ke login
header("Location: ../index.php?halaman=login");
exit();
?>
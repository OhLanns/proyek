<?php
session_start();
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Tentukan redirect URL
            $redirect_url = 'index.php?halaman=home'; // Default
            
             if (isset($_SESSION['redirect_url'])) {
                // Jika berasal dari tombol beli, arahkan ke menu
                if (isset($_SESSION['from_beli']) && $_SESSION['from_beli'] === true) {
                    $redirect_url = 'index.php?halaman=menu';
                } else {
                    $redirect_url = $_SESSION['redirect_url'];
                }
                
                unset($_SESSION['redirect_url']);
                unset($_SESSION['from_beli']);
            }

            header("Location:../" . $redirect_url);
            exit();
        } else {
            $_SESSION['login_error'] = "Password salah";
        }
    } else {
        $_SESSION['login_error'] = "Username tidak ditemukan";
    }
}

header("Location: ../index.php?halaman=login");
exit();
?>
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
            
            // SELALU redirect ke home setelah login
            header("Location: ../index.php?halaman=home");
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
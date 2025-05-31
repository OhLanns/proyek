<?php

// Proses login jika form dikirim
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
            $_SESSION['role'] = $user['role'];
            
            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin/index.php?page=home");
            } else {
                header("Location: index.php?halaman=home");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Password salah";
        }
    } else {
        $_SESSION['login_error'] = "Username tidak ditemukan";
    }

    // Redirect kembali ke halaman login jika ada error
    header("Location: index.php?halaman=login");
    exit();
}
?>

<div class="login-wrapper">
    <div class="login-card">
        <h4 class="text-center mb-4">MASUK</h4>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['login_error']) ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['need_login'])): ?>
            <div class="alert alert-info">Silakan login terlebih dahulu untuk melanjutkan.</div>
        <?php endif; ?>

        <form action="index.php?halaman=login" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 mb-3">Masuk</button>
            <div class="mb-3 d-flex justify-content-between">
                <a href="?halaman=lupa_password" class="text-decoration-none">Lupa kata sandi?</a>
            </div>
        </form>

        <p class="text-center">Belum punya akun? <a href="index.php?halaman=signup" class="text-decoration-none">Daftar sekarang</a></p>
    </div> 
</div>
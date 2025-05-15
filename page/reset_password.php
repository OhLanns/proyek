<style>
    .akun{
        margin-top:-20px;
    }
</style>
<?php
session_start();

$message = '';
$valid_token = false;
$token = isset($_GET['token']) ? $conn->real_escape_string($_GET['token']) : '';

// Validasi token
if (!empty($token)) {
    $sql = "SELECT id, reset_token_expires FROM users WHERE reset_token = '$token'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $expires = strtotime($user['reset_token_expires']);
        
        if (time() < $expires) {
            $valid_token = true;
            $user_id = $user['id'];
        } else {
            $message = "Link reset password sudah kadaluarsa.";
        }
    } else {
        $message = "Token reset tidak valid.";
    }
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if ($password !== $confirm_password) {
        $message = "Password dan konfirmasi password tidak sama.";
    } elseif (strlen($password) < 6) {
        $message = "Password minimal 6 karakter.";
    } else {
        // Hash password baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password dan hapus token
        $sql = "UPDATE users SET 
                password = '$hashed_password', 
                reset_token = NULL, 
                reset_token_expires = NULL 
                WHERE id = $user_id";
        
        if ($conn->query($sql)) {
            $message = "Password berhasil direset. Silakan login dengan password baru Anda.";
            $success = true;
        } else {
            $message = "Terjadi kesalahan saat mereset password. Silakan coba lagi.";
        }
    }
}
?>

<div class="login-wrapper akun">
    <div class="login-card">
        <h4 class="text-center mb-4">RESET PASSWORD</h4>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= isset($success) ? 'success' : 'danger' ?>">
                <?= htmlspecialchars($message) ?>
                <?php if (isset($success)): ?>
                    <div class="mt-2">
                        <a href="index.php?halaman=login" class="btn btn-primary w-100">Login Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($valid_token && empty($success)): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-warning w-100 mb-3">Reset Password</button>
            </form>
        <?php elseif (empty($message)): ?>
            <div class="alert alert-danger">
                Token reset tidak valid atau tidak ditemukan.
            </div>
        <?php endif; ?>
    </div>
</div>
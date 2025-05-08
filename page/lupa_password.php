<style>
    .akun{
        margin-top:-20px;
    }
</style>
<?php
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    
    // Cek apakah email terdaftar
    $sql = "SELECT id, username FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // Berlaku 1 jam
        
        // Simpan token ke database
        $update_sql = "UPDATE users SET reset_token = '$token', reset_token_expires = '$expires' 
                      WHERE id = " . $user['id'];
        
        if ($conn->query($update_sql)) {
            // Kirim email dengan token
            $reset_link = "http://".$_SERVER['HTTP_HOST']."/index.php?halaman=reset_password&token=$token";
            
            // Dalam produksi, gunakan PHPMailer atau library email
            // Ini contoh untuk development:
            $email_content = "Halo ".$user['username'].",\n\n";
            $email_content .= "Anda menerima email ini karena meminta reset password.\n\n";
            $email_content .= "Silakan klik link berikut untuk reset password:\n";
            $email_content .= $reset_link."\n\n";
            $email_content .= "Link ini berlaku 1 jam.\n";
            $email_content .= "Jika Anda tidak meminta reset, abaikan email ini.\n";
            
            // Simpan email ke session untuk preview (development only)
            $_SESSION['email_preview'] = [
                'to' => $email,
                'subject' => 'Reset Password - Dapur Aizlan',
                'content' => $email_content,
                'token' => $token // Simpan token untuk keperluan development
            ];
            
            $message = "Link reset password telah dikirim ke email Anda. Silakan cek inbox Anda.";
        } else {
            $message = "Gagal menyimpan token. Silakan coba lagi.";
        }
    } else {
        $message = "Email tidak terdaftar dalam sistem.";
    }
}
?>

<div class="login-wrapper akun">
    <div class="login-card">
        <h4 class="text-center mb-4">LUPA PASSWORD</h4>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= strpos($message, 'terkirim') !== false ? 'success' : 'danger' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['email_preview'])): ?>
            <div class="alert alert-info">
                <h5>Email Preview (Development Mode)</h5>
                <p><strong>Kepada:</strong> <?= htmlspecialchars($_SESSION['email_preview']['to']) ?></p>
                <p><strong>Subjek:</strong> <?= htmlspecialchars($_SESSION['email_preview']['subject']) ?></p>
                <pre><?= htmlspecialchars($_SESSION['email_preview']['content']) ?></pre>
                <p class="mt-3">
                    <strong>Token Reset:</strong> <?= htmlspecialchars($_SESSION['email_preview']['token']) ?>
                    <br>
                    <strong>Link Reset:</strong> 
                    <a href="index.php?halaman=reset_password&token=<?= htmlspecialchars($_SESSION['email_preview']['token']) ?>">
                        Klik di sini untuk reset password
                    </a>
                </p>
            </div>
            <?php unset($_SESSION['email_preview']); ?>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Terdaftar</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 mb-3">Kirim Link Reset Password</button>
        </form>
        
        <div class="text-center">
            <a href="index.php?halaman=login" class="text-decoration-none">Kembali ke Login</a>
        </div>
    </div>
</div>
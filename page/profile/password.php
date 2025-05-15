<?php

// Inisialisasi pesan
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $user_id = $_POST['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password baru
    if (strlen($new_password) < 6) {
        $message = "Password baru harus minimal 6 karakter";
        $message_type = "danger";
    } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $message = "Password baru harus mengandung huruf dan angka";
        $message_type = "danger";
    } elseif ($new_password !== $confirm_password) {
        $message = "Konfirmasi password tidak cocok";
        $message_type = "danger";
    } else {
        // Ambil password saat ini dari database
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password saat ini
            if (password_verify($current_password, $user['password'])) {
                // Hash password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password di database
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $message = "Password berhasil diubah";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengubah password";
                    $message_type = "danger";
                }
                
                $update_stmt->close();
            } else {
                $message = "Password saat ini salah";
                $message_type = "danger";
            }
        } else {
            $message = "User tidak ditemukan";
            $message_type = "danger";
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ganti Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
    }
    
    .password-container {
      max-width: 500px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 20px;
    }
    
    .section-header {
      background-color: orange;
      padding: 15px;
      border-radius: 8px 8px 0 0;
      font-size: 1.5rem;
      font-weight: bold;
      color: #000;
      text-align: center;
      margin-bottom: 16px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .btn-submit {
      background-color: orange;
      color: black;
      font-weight: bold;
      border: none;
      width: 100%;
      padding: 10px;
      border-radius: 8px;
    }
    
    .btn-submit:hover {
      background-color: #e68a00;
    }
    
    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="password-container">
    <div class="section-header">Ganti Password</div>
    
    <?php
    // Tampilkan pesan error/sukses
    if (!empty($message)) {
        echo '<div class="alert alert-'.$message_type.'">'.$message.'</div>';
    }
    
    // Cek apakah user sudah login
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): 
        $user_id = $_SESSION['user_id'];
    ?>
    
    <form method="post">
      <div class="form-group">
        <label for="current_password">Password Saat Ini</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
      </div>
      
      <div class="form-group">
        <label for="new_password">Password Baru</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
        <small class="text-muted">Minimal 6 karakter, mengandung huruf dan angka</small>
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Konfirmasi Password Baru</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      </div>
      
      <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
      <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
    </form>
    
    <a href="index.php?halaman=profile" class="back-link">Kembali ke Profil</a>
    
    <?php else: ?>
      <div class="alert alert-warning">Silakan login terlebih dahulu</div>
      <a href="index.php?halaman=login" class="btn btn-warning">Login</a>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
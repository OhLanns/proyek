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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Username Form</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #F5F5F5;
    }

    .header {
      background-color: #FFA500;
      color: black;
      padding: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: bold;
      font-size: 20px;
    }

    .header .back {
      font-size: 24px;
      cursor: pointer;
    }

    .header .check {
      font-size: 24px;
      cursor: pointer;
    }

    .form-container {
      background-color: #FFA500;
      padding: 20px;
      margin-top: 20px;
    }

    .form-container1 {
      background-color: #FFA500;
      padding: 20px;
    }

    .input-group {
      background-color: white;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .input-group input {
      border: none;
      flex: 1;
      padding: 8px;
      font-size: 16px;
    }

    .input-group span {
      margin-right: 10px;
    }

    .button-container {
      background-color: #D3D3D3;
      padding: 40px;
      text-align: center;
    }

    .btn-confirm {
      background-color: white;
      color: black;
      padding: 10px 24px;
      border-radius: 20px;
      font-weight: bold;
      border: none;
      cursor: pointer;
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

</body>
</html>

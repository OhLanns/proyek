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
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Poppins', sans-serif;
    }

    .password-container {
      max-width: 500px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 25px;
    }

    .section-header {
      background-color: #FFA500;
      padding: 15px;
      border-radius: 8px;
      font-size: 1.3rem;
      font-weight: bold;
      color: #000;
      text-align: center;
      margin-bottom: 25px;
    }

    .form-label {
      font-weight: 500;
      margin-bottom: 5px;
    }

    .form-control {
      border-radius: 8px;
      padding: 10px 15px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
    }

    .form-control:focus {
      border-color: #FFA500;
      box-shadow: 0 0 0 0.25rem rgba(255, 165, 0, 0.25);
    }

    .btn-submit {
      background-color: #FFA500;
      color: #000;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      width: 100%;
      margin-top: 10px;
      transition: all 0.3s;
    }

    .btn-submit:hover {
      background-color: #e69500;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #555;
      text-decoration: none;
    }

    .back-link:hover {
      color: #FFA500;
      text-decoration: underline;
    }

    .password-strength {
      font-size: 0.85rem;
      margin-top: -10px;
      margin-bottom: 15px;
    }
  </style>
  <div class="password-container" style="margin-top: 100px;">
    <div class="section-header">Ganti Password</div>
    
    <?php if (!empty($message)): ?>
      <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
      <?php $user_id = $_SESSION['user_id']; ?>
      
      <form method="post">
        <div class="mb-3">
          <label for="current_password" class="form-label">Password Saat Ini</label>
          <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        
        <div class="mb-3">
          <label for="new_password" class="form-label">Password Baru</label>
          <input type="password" class="form-control" id="new_password" name="new_password" required>
          <div class="password-strength text-muted">Minimal 6 karakter</div>
        </div>
        
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        <button type="submit" class="btn btn-submit">
          Simpan Perubahan
        </button>
      </form>
      
      <a href="index.php?halaman=profile" class="back-link">
         Kembali ke Profil
      </a>
      
    <?php else: ?>
      <div class="alert alert-warning d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>Silakan login terlebih dahulu</div>
      </div>
      <a href="index.php?halaman=login" class="btn btn-warning w-100">
        <i class="bi bi-box-arrow-in-right"></i> Login
      </a>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Simple password strength indicator
    document.getElementById('new_password').addEventListener('input', function() {
      const password = this.value;
      const strengthText = document.querySelector('.password-strength');
      
      if (password.length === 0) {
        strengthText.textContent = 'Minimal 6 karakter';
        strengthText.className = 'password-strength text-muted';
      } else if (password.length < 6) {
        strengthText.textContent = 'Password terlalu pendek';
        strengthText.className = 'password-strength text-danger';
      } else {
        strengthText.textContent = 'Password cukup kuat';
        strengthText.className = 'password-strength text-success';
      }
    });
  </script>
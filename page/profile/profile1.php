<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengaturan Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
    }

    .pengaturan-container {
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

    .user-info {
      padding: 15px;
      margin-bottom: 20px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }

    .user-info-item {
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
    }

    .user-info-label {
      font-weight: bold;
      color: #555;
    }

    .user-info-value {
      color: #333;
    }

    .profil-section .section-header {
      background-color: #ff9900;
    }

    .menu-item {
      background-color: orange;
      color: black;
      margin-bottom: 10px;
      padding: 12px 20px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
    }

    .action-buttons {
      margin-top: 30px;
    }

    .action-buttons button {
      width: 100%;
      margin-bottom: 15px;
      padding: 10px;
      font-weight: bold;
      border-radius: 8px;
    }
  </style>
</head>
<body>

  <div class="pengaturan-container">
    <div class="section-header">Pengaturan Akun</div>

    <?php
    
    // Cek apakah user sudah login
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // Ambil user_id dari session
        $user_id = $_SESSION['user_id'];
        
        // Query untuk mendapatkan data user
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            ?>
            <div class="user-info">
                <div class="user-info-item">
                    <span class="user-info-label">Nama:</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($user['nama'] ?? '-'); ?></span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Username:</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($user['username'] ?? '-'); ?></span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Email:</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($user['email'] ?? '-'); ?></span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Nomor Telepon:</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($user['no_telepon'] ?? '-'); ?></span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Alamat:</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($user['alamat'] ?? '-'); ?></span>
                </div>
            </div>
            <?php
        } else {
            echo '<div class="alert alert-warning">Data pengguna tidak ditemukan</div>';
        }
        
        $stmt->close();
    } else {
        echo '<div class="alert alert-warning">Silakan login terlebih dahulu</div>';
    }
    ?>
    <div class="user-actions mt-3">
        <a href="index.php?halaman=update_akun" class="btn btn-warning btn-sm">Edit Akun</a>
        <a href="index.php?halaman=delete_akun" class="btn btn-danger btn-sm">Hapus Akun</a>
    </div>

    <!-- <div class="menu-item" onclick="window.location.href='?halaman=keamanan'">Keamanan & Akun <i class="bi bi-chevron-right"></i></div>
    <div class="menu-item" onclick="window.location.href='?halaman=alamat'">Alamat Saya <i class="bi bi-chevron-right"></i></div>
    <div class="menu-item" onclick="window.location.href='?halaman=bank'">Kartu / Rekening Bank <i class="bi bi-chevron-right"></i></div> -->

    <div class="action-buttons">
      <a href="index.php?halaman=login"><button class="btn btn-light border">Ganti Akun</button></a>
      <a href="index.php?halaman=logout"><button class="btn btn-light border">Logout</button></a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php

// Cek apakah user sudah login
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../index.php?halaman=login");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = '';

// Proses penghapusan akun
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    
    // Verifikasi password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0) {
        $error_message = "Akun tidak ditemukan";
    } else {
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if(!password_verify($password, $user['password'])) {
            $error_message = "Password salah";
        } else {
            // Lanjutkan penghapusan akun
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            
            if($stmt->execute()) {
                // Hapus semua session
                session_unset();
                session_destroy();
                
                header("Location: ../../index.php?halaman=login&account_deleted=1");
                exit();
            } else {
                $error_message = "Gagal menghapus akun: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .delete-container {
            max-width: 500px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .section-header {
            background-color: #dc3545;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            text-align: center;
            margin-bottom: 16px;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    
    <div class="delete-container">
        <div class="section-header">Hapus Akun</div>
        
        <?php if(!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="warning-message">
            <h5>Peringatan!</h5>
            <p>Anda akan menghapus akun Anda secara permanen. Semua data yang terkait dengan akun ini akan dihapus dan tidak dapat dikembalikan.</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="password">Masukkan Password untuk Konfirmasi</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-danger w-100">Hapus Akun Permanen</button>
            <a href="../../index.php?halaman=profile" class="btn btn-light w-100 mt-2">Batalkan</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
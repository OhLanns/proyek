<?php
include "../../config.php";

// Cek apakah user sudah login
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../index.php?halaman=login");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user saat ini
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Proses form update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];

    // Validasi input
    $errors = [];
    if(empty($nama)) $errors[] = "Nama harus diisi";
    if(empty($username)) $errors[] = "Username harus diisi";
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";

    if(empty($errors)) {
        // Update data user
        $sql = "UPDATE users SET nama=?, username=?, email=?, no_telepon=?, alamat=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nama, $username, $email, $no_telepon, $alamat, $user_id);
        
        if($stmt->execute()) {
            $_SESSION['success_message'] = "Profil berhasil diperbarui";
            header("Location: ../../index.php?halaman=profile");
            exit();
        } else {
            $errors[] = "Gagal memperbarui profil: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .edit-container {
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
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: orange;
            border-color: orange;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="section-header">Edit Profil</div>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" 
                       value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="no_telepon">Nomor Telepon</label>
                <input type="tel" class="form-control" id="no_telepon" name="no_telepon" 
                       value="<?php echo htmlspecialchars($user['no_telepon'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php 
                    echo htmlspecialchars($user['alamat'] ?? ''); 
                ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            <a href="../profile1.php" class="btn btn-light w-100 mt-2">Kembali</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include "../config.php";

// Inisialisasi variabel
$error = '';
$success = '';
$menu = [];

// Validasi ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID menu tidak valid");
}

$id = $_GET['id'];

// Ambil data menu yang akan diupdate
$stmt = $conn->prepare("SELECT * FROM menu WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$menu = $result->fetch_assoc();

// Proses form update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    
    // Handle file upload
    $img_name = $menu['img']; // Default ke gambar lama
    
    if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../gambar/menu/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validasi file gambar
        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if ($check === false) {
            $error = "File yang diupload bukan gambar.";
        } elseif ($_FILES["img"]["size"] > 10000000) {
            $error = "Ukuran gambar terlalu besar (max 10MB).";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
        } else {
            // Generate nama unik untuk file
            $img_name = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $img_name;
            
            // Hapus gambar lama jika ada
            if ($menu['img'] && file_exists($target_dir . $menu['img'])) {
                unlink($target_dir . $menu['img']);
            }
            
            // Upload gambar baru
            if (!move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $error = "Terjadi kesalahan saat mengupload gambar.";
                $img_name = $menu['img']; // Kembali ke gambar lama jika upload gagal
            }
        }
    }
    
    // Update data jika tidak ada error
    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE menu SET judul=?, deskripsi=?, harga=?, img=? WHERE id=?");
        $stmt->bind_param("ssisi", $judul, $deskripsi, $harga, $img_name, $id);

        if ($stmt->execute()) {
            $success = "Data menu berhasil diupdate!";
            // Refresh data
            $stmt = $conn->prepare("SELECT * FROM menu WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $menu = $result->fetch_assoc();
        } else {
            $error = "Gagal mengupdate data: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap" rel="stylesheet" />
</head>
<body>
    <section class="menu py-5">
        <div class="lebar" style="height: 60px;"></div>
        <div class="container">
            <h2 class="text-center mb-4 menukami"><b>Edit Menu</b></h2>
            
            <!-- Notifikasi -->
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <div class="card p-4">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Nama Menu</label>
                        <input type="text" class="form-control" id="judul" name="judul" 
                               value="<?= htmlspecialchars($menu['judul']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?= 
                            htmlspecialchars($menu['deskripsi']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" 
                               value="<?= htmlspecialchars($menu['harga']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="img" class="form-label">Gambar Menu</label>
                        <input type="file" class="form-control" id="img" name="img" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                        <?php if ($menu['img']): ?>
                            <div class="mt-2">
                                <img src="../gambar/menu/<?= htmlspecialchars($menu['img']) ?>" 
                                     alt="Gambar Menu" style="max-width: 200px; max-height: 200px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Update Menu</button>
                        <a href="readmenu.php" class="btn btn-secondary">Kembali ke Daftar Menu</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
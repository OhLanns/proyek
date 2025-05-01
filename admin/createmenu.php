<?php
include "../config.php";

// Inisialisasi variabel
$error = '';
$success = '';

// Proses form tambah menu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $img_name = ''; // Default kosong
    
    // Handle file upload (wajib untuk menu baru)
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
            
            // Upload gambar
            if (!move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $error = "Terjadi kesalahan saat mengupload gambar.";
            }
        }
    } else {
        $error = "Gambar menu wajib diupload.";
    }
    
    // Insert data jika tidak ada error
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO menu (judul, deskripsi, harga, img) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $judul, $deskripsi, $harga, $img_name);

        if ($stmt->execute()) {
            $success = "Menu baru berhasil ditambahkan!";
            // Reset form
            $judul = $deskripsi = $harga = '';
        } else {
            $error = "Gagal menambahkan menu: " . $conn->error;
            // Hapus gambar yang sudah diupload jika insert gagal
            if ($img_name && file_exists($target_file)) {
                unlink($target_file);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - Dapur Aizlan</title>
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
            <h2 class="text-center mb-4 menukami"><b>Tambah Menu Baru</b></h2>
            
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
                               value="<?= isset($judul) ? htmlspecialchars($judul) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?= 
                            isset($deskripsi) ? htmlspecialchars($deskripsi) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" 
                               value="<?= isset($harga) ? htmlspecialchars($harga) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="img" class="form-label">Gambar Menu</label>
                        <input type="file" class="form-control" id="img" name="img" accept="image/*" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG, GIF (max 10MB)</small>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Simpan Menu</button>
                        <a href="readmenu.php" class="btn btn-secondary">Kembali ke Daftar Menu</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
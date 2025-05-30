<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?halaman=login");
    exit();
}

// Proses tambah menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['judul'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $harga = $conn->real_escape_string($_POST['harga']);
    
    // Handle file upload
    $target_dir = "../gambar/menu/"; // Perbaikan path
    
    // Pastikan folder tujuan ada
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            die("<script>alert('Gagal membuat direktori gambar');</script>");
        }
    }
    
    // Generate unique filename
    $file_ext = strtolower(pathinfo($_FILES["img"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;
    
    // Validasi upload
    $uploadOk = true;
    $error_message = '';
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES["img"]["tmp_name"]);
    if($check === false) {
        $uploadOk = false;
        $error_message = "File bukan gambar.";
    }
    
    // Check file size (max 2MB)
    if ($_FILES["img"]["size"] > 2000000) {
        $uploadOk = false;
        $error_message = "Ukuran gambar terlalu besar. Maksimal 2MB.";
    }
    
    // Allow certain file formats
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if(!in_array($file_ext, $allowed_extensions)) {
        $uploadOk = false;
        $error_message = "Hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
    }
    
    // Check if $uploadOk is set to false by an error
    if ($uploadOk) {
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
            // Insert to database
            $sql = "INSERT INTO menu (judul, deskripsi, harga, img) VALUES ('$judul', '$deskripsi', '$harga', '$new_filename')";
            if ($conn->query($sql)) {
                echo "<script>
                    alert('Menu berhasil ditambahkan!');
                    window.location.href = 'index.php?page=menu';
                </script>";
            } else {
                // Hapus file yang sudah diupload jika query gagal
                unlink($target_file);
                echo "<script>alert('Error database: " . addslashes($conn->error) . "');</script>";
            }
        } else {
            $error = error_get_last();
            echo "<script>alert('Gagal mengupload gambar: " . addslashes($error['message']) . "');</script>";
        }
    } else {
        echo "<script>alert('$error_message');</script>";
    }
}

// Proses hapus menu
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    // Ambil nama file gambar
    $sql = "SELECT img FROM menu WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $img_file = "../gambar/menu/" . $row['img'];
        
        // Hapus dari database
        $sql = "DELETE FROM menu WHERE id = $id";
        if ($conn->query($sql)) {
            // Hapus file gambar jika ada
            if (file_exists($img_file)) {
                unlink($img_file);
            }
            echo "<script>
                alert('Menu berhasil dihapus!');
                window.location.href = 'index.php?page=menu';
            </script>";
        } else {
            echo "<script>alert('Gagal menghapus menu: " . addslashes($conn->error) . "');</script>";
        }
    }
}

// Proses edit menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $judul = $conn->real_escape_string($_POST['edit_judul']);
    $deskripsi = $conn->real_escape_string($_POST['edit_deskripsi']);
    $harga = $conn->real_escape_string($_POST['edit_harga']);
    
    // Jika ada file gambar baru diupload
    if (!empty($_FILES['edit_img']['name'])) {
        $target_dir = "../gambar/menu/";
        $file_ext = strtolower(pathinfo($_FILES["edit_img"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        // Validasi upload
        $uploadOk = true;
        $check = getimagesize($_FILES["edit_img"]["tmp_name"]);
        if($check === false) {
            $uploadOk = false;
            echo "<script>alert('File bukan gambar.');</script>";
        }
        
        if ($_FILES["edit_img"]["size"] > 2000000) {
            $uploadOk = false;
            echo "<script>alert('Ukuran gambar terlalu besar. Maksimal 2MB.');</script>";
        }
        
        if ($uploadOk && move_uploaded_file($_FILES["edit_img"]["tmp_name"], $target_file)) {
            // Dapatkan nama file lama untuk dihapus
            $sql = "SELECT img FROM menu WHERE id = $id";
            $result = $conn->query($sql);
            $old_img = $result->fetch_assoc()['img'];
            
            // Update database dengan gambar baru
            $sql = "UPDATE menu SET judul='$judul', deskripsi='$deskripsi', harga='$harga', img='$new_filename' WHERE id=$id";
            
            if ($conn->query($sql)) {
                // Hapus file lama jika update berhasil
                if (!empty($old_img)) {
                    $old_file = $target_dir . $old_img;
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                echo "<script>
                    alert('Menu berhasil diupdate!');
                    window.location.href = 'index.php?page=menu';
                </script>";
            } else {
                // Hapus file yang baru diupload jika query gagal
                unlink($target_file);
                echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
            }
        }
    } else {
        // Update tanpa mengganti gambar
        $sql = "UPDATE menu SET judul='$judul', deskripsi='$deskripsi', harga='$harga' WHERE id=$id";
        if ($conn->query($sql)) {
            echo "<script>
                alert('Menu berhasil diupdate!');
                window.location.href = 'index.php?page=menu';
            </script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-sidebar {
            background-color: var(--primary);
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding-top: 20px;
        }
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar .brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .admin-sidebar .nav-link {
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }
        .admin-sidebar .nav-link:hover, 
        .admin-sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
        }
        .admin-sidebar .nav-link i {
            margin-right: 10px;
        }
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .img-preview {
            max-width: 150px;
            max-height: 150px;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div>
                <div class="brand">
                    <h4><i class="bi bi-egg-fried"></i> Dapur Aizlan</h4>
                    <small>Admin Panel</small>
                </div>
                <ul class="nav flex-column sidebar-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=menu">
                            <i class="bi bi-book"></i> Kelola Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=kelola_user">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=pesanan">
                            <i class="bi bi-receipt"></i> Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=laporan">
                            <i class="bi bi-graph-up"></i> Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php?halaman=logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="admin-main">
            <h2 class="mb-4"><i class="bi bi-book"></i> Kelola Menu</h2>
            <div class="mb-3">
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahMenu">
                <i class="bi bi-plus-circle me-2"></i>
                <span>Tambah Menu</span>
            </button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Menu</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM menu ORDER BY id DESC";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                        echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                        echo "<td><img src='../gambar/menu/" . htmlspecialchars($row['img']) . "' alt='" . htmlspecialchars($row['judul']) . "' class='img-thumbnail' style='max-width: 100px;'></td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-sm btn-warning me-2 edit-btn' data-id='" . $row['id'] . "' data-judul='" . htmlspecialchars($row['judul']) . "' data-deskripsi='" . htmlspecialchars($row['deskripsi']) . "' data-harga='" . $row['harga'] . "' data-img='" . $row['img'] . "' style='min-width:110px; margin-bottom:5px;'> Edit Menu</button>";
                                        echo "<a href='index.php?page=menu&hapus=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus menu ini?')\" style='min-width:110px;'>Hapus Menu</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Tidak ada data menu</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Menu -->
    <div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-labelledby="modalTambahMenuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahMenuLabel">Tambah Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="index.php?page=menu" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="judul" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="img" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="img" name="img" accept="image/*" required>
                            <div class="form-text">Ukuran maksimal 2MB. Format: JPG, JPEG, PNG</div>
                            <img id="imgPreview" src="#" alt="Preview Gambar" class="img-preview d-none">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div class="modal fade" id="modalEditMenu" tabindex="-1" aria-labelledby="modalEditMenuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditMenuLabel">Edit Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="index.php?page=menu" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_judul" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="edit_judul" name="edit_judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="edit_deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="edit_harga" name="edit_harga" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_img" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="edit_img" name="edit_img" accept="image/*">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar</div>
                            <img id="editImgPreview" src="#" alt="Preview Gambar" class="img-preview">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tampilkan modal jika ada error
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['judul'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('modalTambahMenu'));
                modal.show();
            });
        <?php endif; ?>

        // Preview gambar saat memilih file
        document.getElementById('img').addEventListener('change', function(e) {
            const preview = document.getElementById('imgPreview');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle tombol edit
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const judul = this.getAttribute('data-judul');
                const deskripsi = this.getAttribute('data-deskripsi');
                const harga = this.getAttribute('data-harga');
                const img = this.getAttribute('data-img');
                
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_judul').value = judul;
                document.getElementById('edit_deskripsi').value = deskripsi;
                document.getElementById('edit_harga').value = harga;
                
                // Set preview gambar
                const preview = document.getElementById('editImgPreview');
                preview.src = '../gambar/menu/' + img;
                preview.classList.remove('d-none');
                
                // Tampilkan modal edit
                const modal = new bootstrap.Modal(document.getElementById('modalEditMenu'));
                modal.show();
            });
        });

        // Preview gambar edit
        document.getElementById('edit_img').addEventListener('change', function(e) {
            const preview = document.getElementById('editImgPreview');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
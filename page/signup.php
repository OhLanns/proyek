<?php

// Proses pendaftaran jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';

    // Validasi input
    if (empty($nama) || empty($username) || empty($email) || empty($password) || empty($konfirmasi_password) || empty($alamat) || empty($no_telepon)) {
        $_SESSION['signup_error'] = "Semua field harus diisi";
        header("Location: ../index.php?halaman=signup");
        exit();
    }

    if ($password !== $konfirmasi_password) {
        $_SESSION['signup_error'] = "Password dan konfirmasi password tidak cocok";
        header("Location: ../index.php?halaman=signup");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['signup_error'] = "Password minimal 6 karakter";
        header("Location: ../index.php?halaman=signup");
        exit();
    }

    // Cek apakah username atau email sudah terdaftar
    $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['username'] === $username) {
            $_SESSION['signup_error'] = "Username sudah digunakan";
        } else {
            $_SESSION['signup_error'] = "Email sudah terdaftar";
        }
        header("Location: ../index.php?halaman=signup");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data ke database
    $insert_query = "INSERT INTO users (nama, username, email, password, alamat, no_telepon, role) 
                    VALUES (?, ?, ?, ?, ?, ?, 'user')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssss", $nama, $username, $email, $hashed_password, $alamat, $no_telepon);

    if ($stmt->execute()) {
        $_SESSION['signup_success'] = "Pendaftaran berhasil! Silakan login";
        header("Location: ../index.php?halaman=login");
    } else {
        $_SESSION['signup_error'] = "Terjadi kesalahan saat pendaftaran";
        header("Location: ../index.php?halaman=signup");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<div class="login-wrapper">
    <div class="login-card">
        <h4 class="text-center mb-4">DAFTAR</h4>

        <?php if (isset($_SESSION['signup_error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['signup_error']) ?>
                <?php unset($_SESSION['signup_error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['signup_success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['signup_success']) ?>
                <?php unset($_SESSION['signup_success']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (min 6 karakter)</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="no_telepon" class="form-label">Nomor Telepon</label>
                <input type="tel" name="no_telepon" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 mb-3">Daftar</button>
            <p class="text-center">Sudah punya akun? <a href="index.php?halaman=login">Login disini</a></p>
        </form>
    </div> 
</div>
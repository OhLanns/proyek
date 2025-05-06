<?php
include "../config.php";

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
    header("Location: index.php?halaman=signup&error=Semua field harus diisi");
    exit();
}

if ($password !== $konfirmasi_password) {
    header("Location: ../index.php?halaman=signup&error=Password dan konfirmasi password tidak cocok");
    exit();
}

if (strlen($password) < 6) {
    header("Location: ../index.php?halaman=signup&error=Password minimal 6 karakter");
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
        header("Location: ../index.php?halaman=signup&error=Username sudah digunakan");
    } else {
        header("Location: ../index.php?halaman=signup&error=Email sudah terdaftar");
    }
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
    header("Location: ../index.php?halaman=login&success=Pendaftaran berhasil! Silakan login");
} else {
    header("Location: ../index.php?halaman=signup&error=Terjadi kesalahan saat pendaftaran");
}

$stmt->close();
$conn->close();
?>
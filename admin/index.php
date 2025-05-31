<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
      rel="stylesheet"
    />
    
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
</head>
<body>
    
<?php
ob_start();
include "koneksi.php";

// Cek jika user mencoba akses halaman admin tanpa login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?halaman=login");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch($page){
    case "home":
        include "admin_page/home.php";
        break;
    case "menu":
        include "admin_page/readmenu.php";
        break;
    case "kelola_user":
        include "admin_page/kelola_user.php";
        break;
    case "pesanan":
        include "admin_page/pesanan.php";
        break;
    case "laporan":
        include "admin_page/laporan.php";
        break;
    default:
        include "admin_page/home.php";
        break;
}
ob_end_flush();
?>
</body>
</html>
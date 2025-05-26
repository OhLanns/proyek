<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="..//bootstrap/css/bootstrap.min.css">
</head>
<body>
    
<?php
session_start();

// Cek jika user mencoba akses halaman admin tanpa login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?halaman=login");
    exit();
}

// Set header untuk mencegah caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch($page){
    case "home":
        include "admin_page/home.php";
        break;
    default:
        include "admin_page/home.php";
        break;
}
?>
<script>
// Mencegah back button browser
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>
</body>
</html>
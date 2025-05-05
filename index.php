<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DAPUR AIZLAN</title>
    <link rel="stylesheet" href="aset/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="aset/style.css" />
    <link rel="stylesheet" href="aset/style2.css" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
    />
      <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
      rel="stylesheet"
    />

</head>
<body>

        <?php
        include "config.php";
        include "template/header.php";
        
        // Mendapatkan parameter halaman dari metode GET
        $page = isset($_GET['halaman']) ? $_GET['halaman'] : 'home';

        // Struktur kontrol untuk menampilkan halaman berdasarkan parameter GET
        switch ($page) {
            case 'home':
                include "page/home.php";
                break;
            case 'profile':
                include "page/profile.php";
                break;
            case 'menu':
                include "page/menu.php";
                break;
            case 'keranjang':
                if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                    include "page/keranjang.php";
                } else {
                    // Simpan URL yang dituju sebelum redirect ke login
                    $_SESSION['redirect_url'] = 'index.php?halaman=keranjang';
                    header("Location: index.php?halaman=login");
                    exit();
                }
                break;
            case 'login':
                include "page/login.php";
                break;
            case 'signup':
                include "page/signup.php";
                break;
            case 'logout':
                include "page/log_out.php";
                break;
            case 'kontak':
                include "page/kontak.php";
                break;
            case 'about':
                include "page/home#about.php";
                break;
            default:
                // Jika parameter tidak cocok, tampilkan halaman 404 atau home
                include "page/home.php";
                break;
        }

        include "template/footer.php";
        ?>


    <script src="aset/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="aset/script.js"></script>
</body>
</html>

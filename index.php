<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DAPUR AIZLAN</title>
    <link rel="stylesheet" href="aset/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="aset/style.css" />
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
        ob_start();
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
                include "page/profile/profile1.php";
                break;
            case 'delete_akun':
                include "page/profile/delete.php";
                break;
            case 'update_akun';
                include "page/profile/update.php";
                break;
            case 'keamanan':
                include "page/profile/keamanan.php";
                break;
            case 'alamat':
                include "page/profile/alamat.php";
                break;
            case 'bank':
                include "page/profile/bank.php";
                break;
            case 'username':
                include "page/profile/username.php";
                break;
            case 'nomer':
                include "page/profile/nomer.php";
                break;
            case 'email':
                include "page/profile/email.php";
                break;
            case 'password':
                include "page/profile/password.php";
                break;
            case 'menu':
                include "page/menu.php";
                break;
            case 'keranjang':
                if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                    include "page/keranjang.php";
                } else {
                    // Redirect langsung ke login TANPA menyimpan URL keranjang
                    header("Location: index.php?halaman=login&need_login=1");
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
            case 'lupa_password':
                include "page/lupa_password.php";
                break;
            case 'reset_password':
                include "page/reset_password.php";
                break;
            case 'riwayat':
                include "page/riwayat.php";
                break;
            default:
                // Jika parameter tidak cocok, tampilkan halaman 404 atau home
                include "page/home.php";
                break;
        }

        include "template/footer.php";
        ob_end_flush();
        ?>


    <script src="aset/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="aset/script.js"></script>
</body>
</html>

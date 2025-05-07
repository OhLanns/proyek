<?php
// save_redirect.php
session_start();

if (isset($_GET['url'])) {
    $_SESSION['redirect_url'] = $_GET['url'];
    
    // Tandai jika ini berasal dari tombol beli
    if (isset($_GET['from']) && $_GET['from'] === 'beli') {
        $_SESSION['from_beli'] = true;
    }
}

echo 'OK';
?>
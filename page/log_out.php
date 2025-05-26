
<?php
include "config.php";

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: index.php?halaman=home");
exit();
?>
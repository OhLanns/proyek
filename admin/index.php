<?php
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
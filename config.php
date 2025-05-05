<?php
session_start();

// Daftar menu dengan ID unik
$servername = "localhost";
$username = "root";
$password = "";
$dbname ="catering";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
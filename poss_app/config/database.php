<?php
$host = "localhost";
$user = "gerard";
$pass = "sistem";
$db   = "pos_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
<?php
session_start();
include '../config/database.php';

// Cek autentikasi
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid");
}

$id = (int)$_GET['id'];

// Gunakan prepared statement untuk delete
$stmt = $conn->prepare("DELETE FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: penjualan.php");
    exit();
} else {
    die("Error: " . $stmt->error);
}
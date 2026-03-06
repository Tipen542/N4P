<?php
session_start();
include 'config/database.php';
if (!isset($_SESSION['user'])) header("Location: index.php");

$total_sales = $conn->query("SELECT SUM(total) as total FROM sales")->fetch_assoc()['total'];
$count_sales = $conn->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="assets/css/style.css">
<title>Dashboard</title>
</head>
<body>

<header>
<h2>Dashboard</h2>
<div>
<a href="sales.php">Penjualan</a>
<a href="auth/logout.php">Logout</a>
</div>
</header>

<div class="container">
<div class="card">
<h3>Total Penjualan</h3>
<p>Rp <?= number_format($total_sales ?? 0) ?></p>
</div>

<div class="card">
<h3>Jumlah Transaksi</h3>
<p><?= $count_sales ?></p>
</div>
</div>

</body>
</html>
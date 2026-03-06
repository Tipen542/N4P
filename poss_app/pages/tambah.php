<?php
session_start();
include '../config/database.php';

// Cek autentikasi
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['simpan'])){
    // Validasi input
    $nama = trim($_POST['nama']);
    $harga = isset($_POST['harga']) ? (float)$_POST['harga'] : 0;
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 0;
    
    if (empty($nama)) {
        $error = "Nama barang tidak boleh kosong";
    } elseif ($harga <= 0) {
        $error = "Harga harus lebih dari 0";
    } elseif ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0";
    } else {
        $total = $harga * $jumlah;
        
        // Gunakan prepared statement untuk mencegah SQL injection
        $stmt = $conn->prepare("INSERT INTO penjualan(nama_barang, harga, jumlah, total) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdid", $nama, $harga, $jumlah, $total);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: penjualan.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>
<link rel="stylesheet" href="../css/style.css">
<div class="container">
<div class="card">
<h2>Tambah Penjualan</h2>
<?php if($error): ?>
    <div style="color: red; margin-bottom: 10px;">❌ <?php echo $error; ?></div>
<?php endif; ?>
<form method="POST">
<input type="text" name="nama" placeholder="Nama Barang" required>
<input type="number" name="harga" placeholder="Harga" required>
<input type="number" name="jumlah" placeholder="Jumlah" required>
<button name="simpan">Simpan</button>
</form>
</div>
</div>
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
$error = '';
$success = '';

// Ambil data dengan prepared statement
$stmt = $conn->prepare("SELECT id, nama_barang, harga, jumlah, total FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data tidak ditemukan");
}

$data = $result->fetch_assoc();
$stmt->close();

if(isset($_POST['update'])){
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
        
        // Gunakan prepared statement untuk update
        $stmt = $conn->prepare("UPDATE penjualan SET nama_barang=?, harga=?, jumlah=?, total=? WHERE id=?");
        $stmt->bind_param("sdidi", $nama, $harga, $jumlah, $total, $id);
        
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
<h2>Edit Penjualan</h2>
<?php if($error): ?>
    <div style="color: red; margin-bottom: 10px;">❌ <?php echo $error; ?></div>
<?php endif; ?>
<form method="POST">
<input type="text" name="nama" value="<?= htmlspecialchars($data['nama_barang'], ENT_QUOTES, 'UTF-8') ?>" required>
<input type="number" name="harga" value="<?= htmlspecialchars($data['harga'], ENT_QUOTES, 'UTF-8') ?>" required>
<input type="number" name="jumlah" value="<?= htmlspecialchars($data['jumlah'], ENT_QUOTES, 'UTF-8') ?>" required>
<button name="update">Update</button>
</form>
</div>
</div>
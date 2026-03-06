<?php
include '../config/database.php';

// Update struktur tabel penjualan
$alterTableSQL = "ALTER TABLE penjualan 
    ADD COLUMN nama_barang VARCHAR(255) AFTER id,
    ADD COLUMN harga DECIMAL(10,2) AFTER nama_barang,
    ADD COLUMN jumlah INT AFTER harga";

// Check if columns exist
$checkColumns = $conn->query("SHOW COLUMNS FROM penjualan WHERE Field IN ('nama_barang', 'harga', 'jumlah')");
$existingColumns = [];
while ($row = $checkColumns->fetch_assoc()) {
    $existingColumns[] = $row['Field'];
}

// Add missing columns
if (!in_array('nama_barang', $existingColumns)) {
    $conn->query("ALTER TABLE penjualan ADD COLUMN nama_barang VARCHAR(255) AFTER id");
}

if (!in_array('harga', $existingColumns)) {
    $conn->query("ALTER TABLE penjualan ADD COLUMN harga DECIMAL(10,2) AFTER nama_barang");
}

if (!in_array('jumlah', $existingColumns)) {
    $conn->query("ALTER TABLE penjualan ADD COLUMN jumlah INT AFTER harga");
}

echo "✅ Database structure updated successfully!";
?>

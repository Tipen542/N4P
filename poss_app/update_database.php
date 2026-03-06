<?php
// File untuk update struktur database
// Jalankan file ini sekali saja http://localhost/poss_app/update_database.php

include 'config/database.php';

echo "Memulai update database...<br><br>";

// Cek apakah kolom bio sudah ada
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'bio'");

if ($result->num_rows == 0) {
    // Tambah kolom bio
    $sql = "ALTER TABLE users ADD COLUMN bio TEXT NULL";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Kolom 'bio' berhasil ditambahkan ke tabel users<br>";
    } else {
        echo "❌ Error menambah kolom: " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Kolom 'bio' sudah ada di tabel users<br>";
}

echo "<br>Selesai! Anda bisa menghapus file ini setelah selesai.";
?>

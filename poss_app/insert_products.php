<?php
include 'config/database.php';

// Update struktur tabel produk - add missing columns
$alterTableSQL = [
    "ALTER TABLE produk ADD COLUMN IF NOT EXISTS image VARCHAR(255) AFTER harga",
    "ALTER TABLE produk ADD COLUMN IF NOT EXISTS deskripsi TEXT AFTER image",
    "ALTER TABLE produk ADD COLUMN IF NOT EXISTS status VARCHAR(50) AFTER deskripsi"
];

foreach ($alterTableSQL as $sql) {
    $conn->query($sql);
}

// Data produk yang akan diinsert
$products = [
    [
        'nama_produk' => 'LS - MAXED BLACK',
        'harga' => 308000,
        'stok' => 0,
        'image' => 'longsleeves.jpg',
        'deskripsi' => 'Koleksi long sleeves premium dengan desain hitam elegan yang sempurna untuk gaya casual-formal.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'TS - GEMZ BLACK',
        'harga' => 273000,
        'stok' => 0,
        'image' => 'tshirt1.jpg',
        'deskripsi' => 'T-shirt dengan desain unik dan printing berkualitas tinggi untuk tampilan kasual yang stylish.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'HD - NOIR BLACK',
        'harga' => 568000,
        'stok' => 0,
        'image' => 'Cap.jpg',
        'deskripsi' => 'Topi bermerek dengan material premium dan desain minimalis yang cocok untuk segala usia.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'HD - RASTER BLACK',
        'harga' => 568000,
        'stok' => 0,
        'image' => 'cap2.jpg',
        'deskripsi' => 'Topi dengan desain raster yang modern dan nyaman dipakai untuk aktivitas sehari-hari.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'TS - DIME BLACK',
        'harga' => 273000,
        'stok' => 0,
        'image' => 'tshirt2.jpg',
        'deskripsi' => 'T-shirt berkualitas dengan jahitan rapi dan bahan yang nyaman untuk penggunaan jangka panjang.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'TS - STREET DREAMS',
        'harga' => 285000,
        'stok' => 0,
        'image' => 'tshirt3.jpg',
        'deskripsi' => 'T-shirt streetwear dengan design eksklusif yang mencerminkan tren fashion terkini.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'HD - CLASSIC BLACK',
        'harga' => 456000,
        'stok' => 10,
        'image' => 'denim.jpg',
        'deskripsi' => 'Celana denim klasik dengan fit yang sempurna dan kualitas bahan yang tahan lama.',
        'status' => 'available'
    ],
    [
        'nama_produk' => 'TS - WORLD WHITE',
        'harga' => 265000,
        'stok' => 0,
        'image' => 'tshirt4.jpg',
        'deskripsi' => 'T-shirt putih premium dengan cutting yang modern dan nyaman untuk berbagai kesempatan.',
        'status' => 'sold_out'
    ],
    [
        'nama_produk' => 'BAG - TRAVEL',
        'harga' => 450000,
        'stok' => 0,
        'image' => 'Bag.jpg',
        'deskripsi' => 'Tas travel dengan kapasitas besar dan desain ergonomis untuk kemudahan perjalanan Anda.',
        'status' => 'sold_out'
    ]
];

// Hapus data lama (optional - untuk testing)
// $conn->query("DELETE FROM produk");

// Insert produk
$inserted = 0;
$skipped = 0;

foreach ($products as $product) {
    // Check if product already exists
    $checkStmt = $conn->prepare("SELECT id FROM produk WHERE nama_produk = ?");
    $checkStmt->bind_param("s", $product['nama_produk']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $skipped++;
        $checkStmt->close();
        continue;
    }
    $checkStmt->close();
    
    // Insert new product
    $stmt = $conn->prepare("INSERT INTO produk (nama_produk, harga, stok, image, deskripsi, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdisss", 
        $product['nama_produk'],
        $product['harga'],
        $product['stok'],
        $product['image'],
        $product['deskripsi'],
        $product['status']
    );
    
    if ($stmt->execute()) {
        $inserted++;
    }
    $stmt->close();
}

echo "✅ Proses selesai!\n";
echo "- Data ditambahkan: $inserted produk\n";
echo "- Data dilewati: $skipped produk (sudah ada)\n";
echo "\nSemua produk telah tersimpan di database!";

$conn->close();
?>

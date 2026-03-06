<?php
session_start();
include '../config/database.php';

// Auto-migrate: Add missing columns to penjualan table
$checkColumns = $conn->query("SHOW COLUMNS FROM penjualan WHERE Field IN ('nama_barang', 'harga', 'jumlah')");
$existingColumns = [];
while ($row = $checkColumns->fetch_assoc()) {
    $existingColumns[] = $row['Field'];
}

if (!in_array('nama_barang', $existingColumns)) {
    $conn->query("ALTER TABLE penjualan ADD COLUMN nama_barang VARCHAR(255) AFTER id");
}

if (!in_array('harga', $existingColumns)) {
    $conn->query("ALTER TABLE penjualan ADD COLUMN harga DECIMAL(10,2) AFTER nama_barang");
}

if (!in_array('jumlah', $existingColumns)) {
    $conn->query("ALTER TABLE penjualan ADD COLUMN jumlah INT AFTER harga");
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_id']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$product_id = (int)$input['product_id'];
$quantity = (int)$input['quantity'];
$price = (float)$input['price'];

// Validate input
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update item in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'quantity' => $quantity,
        'price' => $price
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'Item added to cart',
    'cart_count' => count($_SESSION['cart'])
]);
?>

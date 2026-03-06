<?php
session_start();
include '../config/database.php';

// Check authentication
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

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

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Product data
$products = [
    1 => ['name' => 'LS - MAXED BLACK', 'image' => 'longsleeves.jpg'],
    2 => ['name' => 'TS - GEMZ BLACK', 'image' => 'tshirt1.jpg'],
    3 => ['name' => 'HD - NOIR BLACK', 'image' => 'Cap.jpg'],
    4 => ['name' => 'HD - RASTER BLACK', 'image' => 'Cap.jpg'],
    5 => ['name' => 'TS - DIME BLACK', 'image' => 'tshirt2.jpg'],
    6 => ['name' => 'TS - STREET DREAMS', 'image' => 'tshirt3.jpg'],
    7 => ['name' => 'HD - CLASSIC BLACK', 'image' => 'denim.jpg'],
    8 => ['name' => 'TS - WORLD WHITE', 'image' => 'tshirt4.jpg'],
    9 => ['name' => 'BAG - TRAVEL', 'image' => 'Bag.jpg'],
];

// Handle remove item
if (isset($_POST['remove_id'])) {
    $remove_id = (int)$_POST['remove_id'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($remove_id) {
        return $item['product_id'] != $remove_id;
    });
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Handle update quantity
if (isset($_POST['update_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    } elseif ($quantity == 0) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($product_id) {
            return $item['product_id'] != $product_id;
        });
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Handle checkout
$checkout_success = false;
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total = $price * $quantity;
        $product_name = $products[$product_id]['name'] ?? 'Unknown Product';
        
        $stmt = $conn->prepare("INSERT INTO penjualan (nama_barang, harga, jumlah, total) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdid", $product_name, $price, $quantity, $total);
        $stmt->execute();
        $stmt->close();
    }
    
    $_SESSION['cart'] = [];
    $checkout_success = true;
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.1; // 10% tax
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - NOT FOR POSER</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ffffff;
        }
        
        .navbar {
            background: linear-gradient(90deg, #000000 0%, #1a1a1a 100%);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .navbar-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: brightness(1.2);
        }
        
        .navbar-brand-name {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .navbar-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .navbar-links a {
            color: #cccccc;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 16px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .navbar-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-bottom: 2px solid #ffffff;
        }
        
        .navbar-links a.active {
            color: #ffffff;
            border-bottom: 2px solid #ffffff;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .page-title {
            font-size: 36px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .cart-wrapper {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .cart-items {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr 100px 80px 40px;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 100%;
            height: 120px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            overflow: hidden;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-info h3 {
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .cart-item-price {
            color: #4caf50;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .cart-item-quantity input {
            width: 50px;
            padding: 5px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: #ffffff;
            text-align: center;
            font-size: 12px;
        }
        
        .cart-item-quantity input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .cart-item-subtotal {
            text-align: right;
            font-weight: bold;
        }
        
        .cart-item-remove {
            background: #ff3333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            padding: 5px 10px;
            transition: 0.3s;
        }
        
        .cart-item-remove:hover {
            background: #ff0000;
        }
        
        .cart-empty {
            padding: 60px 20px;
            text-align: center;
            color: #999999;
        }
        
        .cart-empty p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        .btn-continue {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }
        
        .btn-continue:hover {
            background: #45a049;
        }
        
        .cart-summary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 25px;
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .summary-row.total {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #4caf50;
        }
        
        .summary-row.total span:first-child {
            color: #ffffff;
        }
        
        .btn-checkout {
            width: 100%;
            padding: 14px;
            background: #4caf50;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-checkout:hover {
            background: #45a049;
        }
        
        .btn-checkout:disabled {
            background: #666666;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .success-message {
            background: #4caf50;
            color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .success-message h2 {
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .cart-wrapper {
                grid-template-columns: 1fr;
            }
            
            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 10px;
            }
            
            .cart-item-quantity, .cart-item-subtotal, .cart-item-remove {
                grid-column: 2;
                justify-self: start;
            }
            
            .cart-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <img src="../assets/logo.png" alt="Logo" class="navbar-logo">
            <span class="navbar-brand-name">NOT FOR POSER</span>
        </div>
        <div class="navbar-links">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="shop.php">🛍️ Shop</a>
            <a href="penjualan.php">📈 Penjualan</a>
            <a href="profil.php">👤 Profil</a>
            <a href="cart.php" class="active">🛒 Cart</a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <?php if ($checkout_success): ?>
            <div class="success-message">
                <h2>✅ Checkout Successful!</h2>
                <p>Your order has been placed successfully. Thank you for shopping at NOT FOR POSER!</p>
            </div>
        <?php endif; ?>
        
        <h1 class="page-title">🛒 Shopping Cart</h1>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="cart-items">
                <div class="cart-empty">
                    <p>Your cart is empty</p>
                    <a href="shop.php" class="btn-continue">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-wrapper">
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <?php 
                            $product = $products[$item['product_id']] ?? ['name' => 'Unknown', 'image' => 'logo.png'];
                            $item_total = $item['price'] * $item['quantity'];
                        ?>
                        <form method="POST" class="cart-item">
                            <div class="cart-item-image">
                                <img src="../assets/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                            </div>
                            <div class="cart-item-info">
                                <h3><?php echo $product['name']; ?></h3>
                                <div class="cart-item-price">IDR <?php echo number_format($item['price'], 0, ',', '.'); ?></div>
                            </div>
                            <div class="cart-item-quantity">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99" onchange="updateQuantity(this.form, <?php echo $item['product_id']; ?>)">
                            </div>
                            <div class="cart-item-subtotal">
                                IDR <?php echo number_format($item_total, 0, ',', '.'); ?>
                            </div>
                            <button type="submit" name="remove_id" value="<?php echo $item['product_id']; ?>" class="cart-item-remove" onclick="return confirm('Remove this item?');">✕</button>
                        </form>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="summary-title">Order Summary</div>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>IDR <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span>IDR <?php echo number_format($tax, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>IDR <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <form method="POST">
                        <button type="submit" name="checkout" class="btn-checkout">Proceed to Checkout</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function updateQuantity(form, productId) {
            const quantity = form.querySelector('input[name="quantity"]').value;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'update_quantity';
            input.value = '1';
            
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            productIdInput.value = productId;
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = quantity;
            
            form.appendChild(input);
            form.appendChild(productIdInput);
            form.appendChild(quantityInput);
            form.submit();
        }
    </script>
</body>
</html>

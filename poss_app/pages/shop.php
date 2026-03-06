<?php
session_start();
include '../config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Data produk dari assets dengan details
$products = [
    [
        'id' => 1,
        'name' => 'LS - MAXED BLACK',
        'image' => 'longsleeves.jpg',
        'price' => 308000,
        'status' => 'sold_out',
        'description' => 'Koleksi long sleeves premium dengan desain hitam elegan yang sempurna untuk gaya casual-formal.',
        'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        'colors' => ['Black']
    ],
    [
        'id' => 2,
        'name' => 'TS - GEMZ BLACK',
        'image' => 'tshirt1.jpg',
        'price' => 273000,
        'status' => 'sold_out',
        'description' => 'T-shirt dengan desain unik dan printing berkualitas tinggi untuk tampilan kasual yang stylish.',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'colors' => ['Black']
    ],
    [
        'id' => 3,
        'name' => 'HD - NOIR BLACK',
        'image' => 'Cap.jpg',
        'price' => 568000,
        'status' => 'sold_out',
        'description' => 'Topi bermerek dengan material premium dan desain minimalis yang cocok untuk segala usia.',
        'sizes' => ['One Size'],
        'colors' => ['Black']
    ],
    [
        'id' => 4,
        'name' => 'HD - RASTER BLACK',
        'image' => 'cap2.jpg',
        'price' => 568000,
        'status' => 'sold_out',
        'description' => 'Topi dengan desain raster yang modern dan nyaman dipakai untuk aktivitas sehari-hari.',
        'sizes' => ['One Size'],
        'colors' => ['Black']
    ],
    [
        'id' => 5,
        'name' => 'TS - DIME BLACK',
        'image' => 'tshirt2.jpg',
        'price' => 273000,
        'status' => 'sold_out',
        'description' => 'T-shirt berkualitas dengan jahitan rapi dan bahan yang nyaman untuk penggunaan jangka panjang.',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'colors' => ['Black']
    ],
    [
        'id' => 6,
        'name' => 'TS - STREET DREAMS',
        'image' => 'tshirt3.jpg',
        'price' => 285000,
        'status' => 'sold_out',
        'description' => 'T-shirt streetwear dengan design eksklusif yang mencerminkan tren fashion terkini.',
        'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        'colors' => ['Black']
    ],
    [
        'id' => 7,
        'name' => 'HD - CLASSIC BLACK',
        'image' => 'denim.jpg',
        'price' => 456000,
        'status' => 'available',
        'description' => 'Celana denim klasik dengan fit yang sempurna dan kualitas bahan yang tahan lama.',
        'sizes' => ['28', '29', '30', '31', '32', '33', '34'],
        'colors' => ['Black']
    ],
    [
        'id' => 8,
        'name' => 'TS - WORLD WHITE',
        'image' => 'tshirt4.jpg',
        'price' => 265000,
        'status' => 'sold_out',
        'description' => 'T-shirt putih premium dengan cutting yang modern dan nyaman untuk berbagai kesempatan.',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'colors' => ['White']
    ],
    [
        'id' => 9,
        'name' => 'BAG - TRAVEL',
        'image' => 'Bag.jpg',
        'price' => 450000,
        'status' => 'sold_out',
        'description' => 'Tas travel dengan kapasitas besar dan desain ergonomis untuk kemudahan perjalanan Anda.',
        'sizes' => ['One Size'],
        'colors' => ['Black']
    ],
];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - NOT FOR POSER</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .shop-header {
            margin-bottom: 50px;
            text-align: center;
        }
        
        .shop-title {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .shop-subtitle {
            color: #999999;
            font-size: 16px;
        }
        
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .filter-left {
            display: flex;
            gap: 20px;
        }
        
        .filter-item {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #cccccc;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .filter-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .filter-item:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .filter-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .sort-text {
            color: #999999;
            font-size: 14px;
            margin-right: 10px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .product-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            animation: fadeIn 0.6s ease;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        
        .product-image {
            width: 100%;
            height: 280px;
            background: rgba(255, 255, 255, 0.05);
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff3333;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            z-index: 10;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }
        
        .product-price {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 15px;
        }
        
        .product-action {
            display: flex;
            gap: 10px;
        }
        
        .btn-add {
            flex: 1;
            padding: 10px;
            background: #ffffff;
            color: #000000;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-add:hover {
            background: #000000;
            color: #ffffff;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }
        
        .btn-add:disabled {
            background: #666666;
            color: #999999;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .btn-like {
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-like:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .item-count {
            color: #999999;
            font-size: 14px;
            text-align: right;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
            }
            
            .navbar-links {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }
            
            .filter-section {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .filter-left, .filter-right {
                flex-wrap: wrap;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }
            
            .product-image {
                height: 180px;
            }
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            animation: fadeIn 0.3s ease;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 0;
            max-width: 800px;
            width: 95%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
        }
        
        .modal-close {
            font-size: 28px;
            cursor: pointer;
            color: #999999;
            border: none;
            background: none;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        
        .modal-close:hover {
            color: #ffffff;
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .modal-body-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }
        
        .modal-image {
            width: 100%;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .modal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .modal-detail {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .modal-detail h2 {
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .modal-price {
            font-size: 28px;
            font-weight: bold;
            color: #4caf50;
        }
        
        .modal-status {
            display: inline-block;
            padding: 6px 12px;
            background: #4caf50;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            width: fit-content;
        }
        
        .modal-status.sold-out {
            background: #ff3333;
        }
        
        .modal-section {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }
        
        .modal-section-title {
            font-size: 14px;
            text-transform: uppercase;
            color: #999999;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .modal-section-content {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .modal-option {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: #cccccc;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
        }
        
        .modal-option:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .modal-option.active {
            background: #ffffff;
            color: #000000;
            border-color: #ffffff;
        }
        
        .modal-description {
            font-size: 14px;
            line-height: 1.6;
            color: #cccccc;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        .modal-btn-primary {
            background: #ffffff;
            color: #000000;
        }
        
        .modal-btn-primary:hover {
            background: #000000;
            color: #ffffff;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }
        
        .modal-btn-primary:disabled {
            background: #666666;
            color: #999999;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .modal-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .modal-quantity-label {
            font-size: 13px;
            color: #cccccc;
            text-transform: uppercase;
        }
        
        .modal-quantity-input {
            width: 60px;
            padding: 6px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: #ffffff;
            text-align: center;
            font-size: 14px;
        }
        
        .modal-quantity-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        /* Notification Toast */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4caf50;
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
            z-index: 2000;
        }
        
        .toast.error {
            background: #ff3333;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .modal-body-row {
                grid-template-columns: 1fr;
            }
            
            .modal-image {
                height: 300px;
            }
            
            .modal-content {
                max-width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="../assets/logo.png" alt="NOT FOR POSER Logo" class="navbar-logo">
            <div class="navbar-brand-name">NOT FOR POSER</div>
        </div>
        <div class="navbar-links">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="shop.php" class="active">🛍️ Shop</a>
            <a href="penjualan.php">📈 Penjualan</a>
            <a href="profil.php">👤 Profil</a>
            <a href="cart.php" style="position: relative;">🛒 Cart <span id="cart-count" style="background: #ff3333; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin-left: 5px; font-weight: bold;">0</span></a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="shop-header">
            <h1 class="shop-title">Shop</h1>
            <p class="shop-subtitle">Koleksi terbaru produk eksklusif NOT FOR POSER</p>
        </div>
        
        <div class="filter-section">
            <div class="filter-left">
                <select id="availability-filter" class="filter-item" onchange="filterProducts()">
                    <option value="">Availability</option>
                    <option value="available">Available Only</option>
                    <option value="sold_out">Sold Out</option>
                    <option value="all">All Products</option>
                </select>
                <select id="price-filter" class="filter-item" onchange="filterProducts()">
                    <option value="">Price Range</option>
                    <option value="0-300000">IDR 0 - 300.000</option>
                    <option value="300000-500000">IDR 300.000 - 500.000</option>
                    <option value="500000-1000000">IDR 500.000 - 1.000.000</option>
                </select>
            </div>
            <div class="filter-right">
                <span class="item-count">Total: <strong id="item-count"><?php echo count($products); ?></strong> items</span>
                <select id="sort-filter" class="filter-item" onchange="filterProducts()">
                    <option value="">Sort By</option>
                    <option value="price-asc">Price (Low to High)</option>
                    <option value="price-desc">Price (High to Low)</option>
                    <option value="name">Name (A to Z)</option>
                    <option value="available">Available First</option>
                </select>
            </div>
        </div>
        
        <div class="products-grid" id="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-id="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-status="<?php echo $product['status']; ?>" onclick="showProductDetail(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)">
                    <div class="product-image">
                        <img src="../assets/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <?php if ($product['status'] === 'sold_out'): ?>
                            <div class="product-badge">Sold out</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-name"><?php echo $product['name']; ?></div>
                        <div class="product-price">IDR <?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                        <div class="product-action" onclick="event.stopPropagation();">
                            <button class="btn-add" onclick="addToCart(<?php echo $product['id']; ?>)" <?php echo $product['status'] === 'sold_out' ? 'disabled' : ''; ?>>
                                <?php echo $product['status'] === 'sold_out' ? 'Sold Out' : 'Add Cart'; ?>
                            </button>
                            <button class="btn-like" title="Tambah ke wishlist" onclick="toggleWishlist(event, <?php echo $product['id']; ?>)">🤍</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Product Detail Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Product Details</div>
                <button class="modal-close" onclick="closeProductModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-body-row">
                    <div class="modal-image">
                        <img id="modalImage" src="" alt="Product">
                    </div>
                    <div class="modal-detail">
                        <h2 id="modalName"></h2>
                        <div class="modal-price" id="modalPrice"></div>
                        <div id="modalStatus" class="modal-status"></div>
                        
                        <div class="modal-section">
                            <div class="modal-section-title">Sizes</div>
                            <div class="modal-section-content" id="modalSizes"></div>
                        </div>
                        
                        <div class="modal-section">
                            <div class="modal-section-title">Colors</div>
                            <div class="modal-section-content" id="modalColors"></div>
                        </div>
                        
                        <div class="modal-section">
                            <div class="modal-section-title">Description</div>
                            <p class="modal-description" id="modalDescription"></p>
                        </div>
                        
                        <div class="modal-footer">
                            <div class="modal-quantity">
                                <label class="modal-quantity-label">Quantity:</label>
                                <input type="number" id="modalQuantity" class="modal-quantity-input" value="1" min="1" max="99">
                            </div>
                            <button class="modal-btn modal-btn-primary" id="modalAddBtn" onclick="confirmAddToCart()">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Product data
        const productsData = <?php echo json_encode($products); ?>;
        let currentProduct = null;
        let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            renderWishlistButtons();
        });
        
        // Filter and Sort Products
        function filterProducts() {
            const availability = document.getElementById('availability-filter').value;
            const priceRange = document.getElementById('price-filter').value;
            const sortBy = document.getElementById('sort-filter').value;
            const grid = document.getElementById('products-grid');
            
            // Filter products
            let filtered = productsData.filter(product => {
                let availabilityMatch = !availability || product.status === availability || availability === 'all';
                let priceMatch = true;
                
                if (priceRange) {
                    const [min, max] = priceRange.split('-').map(Number);
                    priceMatch = product.price >= min && product.price <= max;
                }
                
                return availabilityMatch && priceMatch;
            });
            
            // Sort products
            if (sortBy === 'price-asc') {
                filtered.sort((a, b) => a.price - b.price);
            } else if (sortBy === 'price-desc') {
                filtered.sort((a, b) => b.price - a.price);
            } else if (sortBy === 'name') {
                filtered.sort((a, b) => a.name.localeCompare(b.name));
            } else if (sortBy === 'available') {
                filtered.sort((a, b) => {
                    if (a.status === 'available' && b.status !== 'available') return -1;
                    if (a.status !== 'available' && b.status === 'available') return 1;
                    return 0;
                });
            }
            
            // Update grid
            grid.innerHTML = filtered.map(product => `
                <div class="product-card" data-id="${product.id}" data-price="${product.price}" data-status="${product.status}" onclick="showProductDetail(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                    <div class="product-image">
                        <img src="../assets/${product.image}" alt="${product.name}">
                        ${product.status === 'sold_out' ? '<div class="product-badge">Sold out</div>' : ''}
                    </div>
                    <div class="product-info">
                        <div class="product-name">${product.name}</div>
                        <div class="product-price">IDR ${new Intl.NumberFormat('id-ID').format(product.price)}</div>
                        <div class="product-action" onclick="event.stopPropagation();">
                            <button class="btn-add" onclick="addToCart(${product.id})" ${product.status === 'sold_out' ? 'disabled' : ''}>
                                ${product.status === 'sold_out' ? 'Sold Out' : 'Add Cart'}
                            </button>
                            <button class="btn-like" title="Tambah ke wishlist" onclick="toggleWishlist(event, ${product.id})">${wishlist.includes(product.id) ? '❤️' : '🤍'}</button>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Update item count
            document.getElementById('item-count').textContent = filtered.length;
            renderWishlistButtons();
        }
        
        // Show Product Detail Modal
        function showProductDetail(product) {
            currentProduct = product;
            document.getElementById('modalTitle').textContent = product.name;
            document.getElementById('modalImage').src = '../assets/' + product.image;
            document.getElementById('modalName').textContent = product.name;
            document.getElementById('modalPrice').textContent = 'IDR ' + new Intl.NumberFormat('id-ID').format(product.price);
            
            const statusEl = document.getElementById('modalStatus');
            statusEl.textContent = product.status === 'available' ? 'Available' : 'Sold Out';
            statusEl.className = 'modal-status' + (product.status === 'sold_out' ? ' sold-out' : '');
            
            // Sizes
            document.getElementById('modalSizes').innerHTML = product.sizes.map((size, idx) => 
                `<div class="modal-option" onclick="this.classList.toggle('active')">${size}</div>`
            ).join('');
            
            // Colors
            document.getElementById('modalColors').innerHTML = product.colors.map(color => 
                `<div class="modal-option" onclick="this.classList.toggle('active')">${color}</div>`
            ).join('');
            
            // Description
            document.getElementById('modalDescription').textContent = product.description;
            
            // Buttons
            const addBtn = document.getElementById('modalAddBtn');
            addBtn.disabled = product.status === 'sold_out';
            
            document.getElementById('productModal').classList.add('show');
        }
        
        // Close Modal
        function closeProductModal() {
            document.getElementById('productModal').classList.remove('show');
        }
        
        // Add to Cart Handler
        function confirmAddToCart() {
            if (currentProduct) {
                const quantity = parseInt(document.getElementById('modalQuantity').value) || 1;
                addToCart(currentProduct.id, quantity);
                closeProductModal();
            }
        }
        
        // Add to Cart
        function addToCart(productId, quantity = 1) {
            const product = productsData.find(p => p.id === productId);
            if (!product) {
                showNotification('Product not found!', 'error');
                return;
            }
            
            if (product.status === 'sold_out') {
                showNotification('This product is sold out!', 'error');
                return;
            }
            
            // Send to backend
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    price: product.price
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`${product.name} added to cart!`, 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message || 'Failed to add to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding to cart', 'error');
            });
        }
        
        // Update Cart Count
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count || 0;
                        cartCount.style.display = data.count > 0 ? 'inline-flex' : 'none';
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Toggle Wishlist
        function toggleWishlist(event, productId) {
            event.stopPropagation();
            const product = productsData.find(p => p.id === productId);
            const index = wishlist.indexOf(productId);
            
            if (index > -1) {
                wishlist.splice(index, 1);
                showNotification(`Removed from wishlist`, 'success');
            } else {
                wishlist.push(productId);
                showNotification(`Added to wishlist: ${product.name}`, 'success');
            }
            
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            renderWishlistButtons();
        }
        
        // Render Wishlist Buttons
        function renderWishlistButtons() {
            document.querySelectorAll('.btn-like').forEach(btn => {
                const card = btn.closest('.product-card');
                const productId = parseInt(card.dataset.id);
                btn.textContent = wishlist.includes(productId) ? '❤️' : '🤍';
            });
        }
        
        // Show Notification
        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Close modal when clicking outside
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
    </script>
</body>
</html>

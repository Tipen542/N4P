<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

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

// Fetch sales data with better query
$data = mysqli_query($conn,"SELECT * FROM penjualan ORDER BY tanggal DESC");

// Calculate stats
$statsQuery = mysqli_query($conn,"SELECT 
    COUNT(*) as total_transactions,
    SUM(total) as total_revenue,
    SUM(jumlah) as total_items
    FROM penjualan");
$stats = mysqli_fetch_assoc($statsQuery);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan - NOT FOR POSER</title>
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
        
        .navbar-container {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
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
        
        .navbar-brand span {
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
        
        .page-header {
            margin-bottom: 40px;
        }
        
        .page-title {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .page-subtitle {
            color: #999999;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideInUp 0.5s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        
        .stat-label {
            color: #999999;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #4caf50;
            margin-bottom: 5px;
        }
        
        .stat-change {
            color: #666666;
            font-size: 12px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: bold;
        }
        
        .section-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .search-box {
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #ffffff;
            font-size: 13px;
            width: 250px;
        }
        
        .search-box::placeholder {
            color: #666666;
        }
        
        .search-box:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .btn-tambah {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.5px;
        }
        
        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.3);
            background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
        }
        
        .table-wrapper {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            animation: slideInUp 0.6s ease;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #cccccc;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table td {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }
        
        .table tr {
            transition: background 0.3s;
        }
        
        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        
        .transaction-id {
            color: #4caf50;
            font-weight: 600;
        }
        
        .product-name {
            font-weight: 600;
            color: #ffffff;
        }
        
        .price {
            color: #4caf50;
            font-weight: 600;
        }
        
        .quantity {
            background: rgba(76, 175, 80, 0.15);
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            color: #4caf50;
        }
        
        .transaction-date {
            color: #999999;
            font-size: 13px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .btn-edit {
            background: rgba(33, 150, 243, 0.15);
            color: #2196F3;
            border: 1px solid rgba(33, 150, 243, 0.3);
        }
        
        .btn-edit:hover {
            background: rgba(33, 150, 243, 0.25);
            border-color: rgba(33, 150, 243, 0.5);
        }
        
        .btn-delete {
            background: rgba(255, 51, 51, 0.15);
            color: #ff3333;
            border: 1px solid rgba(255, 51, 51, 0.3);
        }
        
        .btn-delete:hover {
            background: rgba(255, 51, 51, 0.25);
            border-color: rgba(255, 51, 51, 0.5);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666666;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .empty-state-text {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        @keyframes slideInUp {
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
            
            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .section-actions {
                width: 100%;
                flex-direction: column;
            }
            
            .search-box {
                width: 100%;
            }
            
            .table {
                font-size: 12px;
            }
            
            .table th, .table td {
                padding: 12px 10px;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-action {
                font-size: 11px;
                padding: 5px 10px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <img src="../assets/logo.png" alt="Logo" class="navbar-logo">
            <span>NOT FOR POSER</span>
        </div>
        <div class="navbar-links">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="shop.php">🛍️ Shop</a>
            <a href="penjualan.php" class="active">📈 Penjualan</a>
            <a href="profil.php">👤 Profil</a>
            <a href="cart.php">🛒 Cart</a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">📈 Data Penjualan</h1>
        <p class="page-subtitle">Kelola dan lihat riwayat semua transaksi penjualan Anda</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">💰 Total Revenue</div>
            <div class="stat-value">IDR <?php echo number_format($stats['total_revenue'] ?? 0, 0, ',', '.'); ?></div>
            <div class="stat-change">Dari semua transaksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">📊 Total Transaksi</div>
            <div class="stat-value"><?php echo $stats['total_transactions'] ?? 0; ?></div>
            <div class="stat-change">Transaksi sukses</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">📦 Total Item Terjual</div>
            <div class="stat-value"><?php echo $stats['total_items'] ?? 0; ?></div>
            <div class="stat-change">Unit produk</div>
        </div>
    </div>
    
    <!-- Sales Table Section -->
    <div class="section-header">
        <h2 class="section-title">Riwayat Transaksi</h2>
        <div class="section-actions">
            <input type="text" class="search-box" id="searchBox" placeholder="Cari nama barang...">
            <a href="tambah.php" class="btn-tambah">➕ Tambah Penjualan</a>
        </div>
    </div>
    
    <div class="table-wrapper">
        <?php if(mysqli_num_rows($data) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php while($row = mysqli_fetch_assoc($data)) { ?>
                        <tr class="data-row" data-product="<?php echo strtolower($row['nama_barang'] ?? ''); ?>">
                            <td><span class="transaction-id">#<?php echo $row['id']; ?></span></td>
                            <td><span class="product-name"><?php echo htmlspecialchars($row['nama_barang'] ?? '-'); ?></span></td>
                            <td><span class="price">IDR <?php echo number_format($row['harga'] ?? 0, 0, ',', '.'); ?></span></td>
                            <td><span class="quantity"><?php echo $row['jumlah'] ?? 0; ?> unit</span></td>
                            <td><span class="price">IDR <?php echo number_format($row['total'] ?? 0, 0, ',', '.'); ?></span></td>
                            <td><span class="transaction-date"><?php echo date('d M Y', strtotime($row['tanggal'] ?? date('Y-m-d'))); ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">✏️ Edit</a>
                                    <a href="hapus.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus data ini?')">🗑️ Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <div class="empty-state-text">Belum ada data penjualan</div>
                <a href="tambah.php" class="btn-tambah">➕ Tambah Data Penjualan Pertama</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchBox').addEventListener('keyup', function(e) {
        const searchValue = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.data-row');
        
        rows.forEach(row => {
            const productName = row.getAttribute('data-product');
            if (productName.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
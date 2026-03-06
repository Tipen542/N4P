<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(total) as total FROM penjualan"));
$count = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as jumlah FROM penjualan"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NOT FOR POSER</title>
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
            box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
            position: relative;
        }
        
        .navbar-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-bottom: 2px solid #ffffff;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
            animation: slideInDown 0.6s ease;
        }
        
        .welcome-title {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }
        
        .welcome-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideInUp 0.6s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffffff 0%, #999999 100%);
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .stat-label {
            color: #999999;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 10px;
        }
        
        .stat-description {
            color: #777777;
            font-size: 13px;
        }
        
        .action-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            animation: slideInUp 0.8s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .action-title {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: #ffffff;
            color: #000000;
            border: 2px solid #ffffff;
        }
        
        .btn-primary:hover {
            background: #000000;
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.6);
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
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
            
            .welcome-title {
                font-size: 28px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
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
            <a href="shop.php">🛍️ Shop</a>
            <a href="penjualan.php">📈 Penjualan</a>
            <a href="profil.php">👤 Profil</a>            <a href="cart.php">🛒 Cart</a>            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <div class="welcome-title">Selamat Datang Kembali, <?= htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8') ?> 👋</div>
            <div class="welcome-subtitle">Mari kelola toko Anda dengan baik hari ini</div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-label">Total Penjualan</div>
                <div class="stat-value">Rp <?= number_format($total['total'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-description">Pendapatan keseluruhan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-label">Jumlah Transaksi</div>
                <div class="stat-value"><?= $count['jumlah'] ?? 0 ?></div>
                <div class="stat-description">Total penjualan yang dilakukan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-label">Hari Ini</div>
                <div class="stat-value"><?= date('d M Y') ?></div>
                <div class="stat-description">Tanggal saat ini</div>
            </div>
        </div>
        
        <div class="action-section">
            <div class="action-title">📋 Aksi Cepat</div>
            <div class="action-buttons">
                <a href="penjualan.php" class="action-btn btn-primary">📈 Lihat Penjualan</a>
                <a href="tambah.php" class="action-btn btn-primary">➕ Tambah Penjualan</a>
                <a href="profil.php" class="action-btn btn-secondary">👤 Edit Profil</a>
                <a href="../auth/logout.php" class="action-btn btn-secondary">🚪 Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
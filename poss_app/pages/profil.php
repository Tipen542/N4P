<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Cek apakah kolom bio ada
$checkBio = $conn->query("SHOW COLUMNS FROM users LIKE 'bio'");
$hasBioColumn = $checkBio->num_rows > 0;

// Jika kolom bio belum ada, tambahkan
if (!$hasBioColumn) {
    $conn->query("ALTER TABLE users ADD COLUMN bio TEXT NULL");
}

// Ambil data profil user
$stmt = $conn->prepare("SELECT username, email, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User tidak ditemukan");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - 🚀 NOT FOR POSER</title>
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
            padding: 20px;
            color: #ffffff;
        }
        
        .navbar {
            background: linear-gradient(90deg, #000000 0%, #1a1a1a 100%);
            padding: 15px 40px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .navbar a {
            color: #cccccc;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .navbar a:hover {
            color: #ffffff;
            border-bottom: 2px solid #ffffff;
        }
        
        .navbar .title {
            font-weight: bold;
            font-size: 18px;
            color: #ffffff;
        }
        
        .navbar .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .container-profil {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .profile-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            animation: slideInUp 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #ffffff 0%, #999999 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
            color: #000000;
        }
        
        .profile-username {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }
        
        .profile-email {
            color: #999999;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .profile-section {
            margin-bottom: 25px;
        }
        
        .section-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .section-content {
            background-color: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            color: #ffffff;
            min-height: 50px;
            display: flex;
            align-items: center;
            border-left: 4px solid #ffffff;
        }
        
        .bio-empty {
            color: #666666;
            font-style: italic;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: #ffffff;
            color: #000000;
            border: 2px solid #ffffff;
            flex: 1;
        }
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.3);
            background: #000000;
            color: #ffffff;
        }
        
        .btn-logout {
            background-color: #ff6b6b;
            color: white;
            border: 2px solid #ff6b6b;
            flex: 1;
        }
        
        .btn-logout:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.3);
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
        
        @media (max-width: 600px) {
            .profile-card {
                padding: 25px;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="title">🚀 NOT FOR POSER</div>
        <div class="right">
            <a href="dashboard.php">Dashboard</a>
            <a href="shop.php">Shop</a>
            <a href="penjualan.php">Penjualan</a>
            <a href="profil.php">Profil</a>
            <a href="cart.php">🛒 Cart</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container-profil">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">👤</div>
                <div class="profile-username"><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            
            <div class="profile-section">
                <div class="section-label">Email</div>
                <div class="section-content">
                    <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
            
            <div class="profile-section">
                <div class="section-label">Bio</div>
                <div class="section-content">
                    <?php 
                    if (!empty($user['bio'])) {
                        echo htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8');
                    } else {
                        echo '<span class="bio-empty">Belum ada bio. Edit profil untuk menambahkan bio.</span>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="button-group">
                <a href="edit_profil.php" class="btn btn-edit">✏️ Edit Profil</a>
                <a href="../auth/logout.php" class="btn btn-logout">🚪 Logout</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

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

// Proses update profil
if (isset($_POST['update_profil'])) {
    $bio = trim($_POST['bio']);
    
    // Validasi bio (max 500 karakter)
    if (strlen($bio) > 500) {
        $error = "Bio tidak boleh lebih dari 500 karakter";
    } else {
        // Update ke database
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->bind_param("si", $bio, $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $success = "Profil berhasil diperbarui!";
            // Refresh data dari database
            $stmt = $conn->prepare("SELECT username, email, bio FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - <?php echo htmlspecialchars($user['username']); ?></title>
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
        
        .container-edit {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .edit-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            animation: slideInUp 0.5s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .edit-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .edit-title {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }
        
        .edit-subtitle {
            color: #999999;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }
        
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.05);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        .char-count {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }
        
        .char-count.warning {
            color: #e74c3c;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.4s ease;
        }
        
        .alert-success {
            background-color: #4caf50;
            color: #ffffff;
            border: 1px solid #45a049;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 107, 107, 0.5);
        }
        
        .button-group {
            display: flex;
            gap: 10px;
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
            flex: 1;
        }
        
        .btn-save {
            background: #ffffff;
            color: #000000;
            border: 2px solid #ffffff;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.3);
            background: #000000;
            color: #ffffff;
        }
        
        .btn-cancel {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-cancel:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
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
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 600px) {
            .edit-card {
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
    
    <div class="container-edit">
        <div class="edit-card">
            <div class="edit-header">
                <div class="edit-title">Edit Profil</div>
                <div class="edit-subtitle">Perbarui informasi profil Anda</div>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <span>✅</span>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span>❌</span>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    <div style="font-size: 12px; color: #999; margin-top: 5px;">Username tidak dapat diubah</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    <div style="font-size: 12px; color: #999; margin-top: 5px;">Email tidak dapat diubah</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Bio (Opsional)</label>
                    <textarea class="form-textarea" name="bio" id="bio" placeholder="Ceritakan tentang diri Anda..."><?php echo htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span>/500 karakter
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="update_profil" class="btn btn-save">💾 Simpan Perubahan</button>
                    <a href="profil.php" class="btn btn-cancel" style="text-decoration: none;">❌ Batal</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        const bioTextarea = document.getElementById('bio');
        const charCount = document.getElementById('charCount');
        
        // Update character count
        bioTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            if (this.value.length > 400) {
                charCount.parentElement.classList.add('warning');
            } else {
                charCount.parentElement.classList.remove('warning');
            }
        });
        
        // Set initial count
        charCount.textContent = bioTextarea.value.length;
        if (bioTextarea.value.length > 400) {
            charCount.parentElement.classList.add('warning');
        }
    </script>
</body>
</html>

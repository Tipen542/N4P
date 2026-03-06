<?php
include '../config/database.php';

$error = '';
$success = '';

if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validasi input
    if(empty($username) || empty($email) || empty($password) || empty($password_confirm)){
        $error = "Semua field harus diisi.";
    } elseif(strlen($password) < 6){
        $error = "Password minimal 6 karakter.";
    } elseif($password !== $password_confirm){
        $error = "Password tidak cocok.";
    } else {
        // Cek apakah username atau email sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $error = "Username atau email sudah terdaftar.";
        } else {
            // Hash password
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert ke database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hashed);
            
            if($stmt->execute()){
                $success = "Registrasi berhasil! Silahkan login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Terjadi kesalahan: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - N4P</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: url('../assets/background.jpg') center/cover no-repeat fixed;
            background-color: #0f0f0f;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            animation: slideInUp 0.6s ease;
        }
        
        .register-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .register-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 25px;
            filter: brightness(1.2);
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .register-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(76, 175, 80, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .register-title {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 10px;
        }
        
        .register-subtitle {
            color: #999999;
            font-size: 14px;
        }
        
        .alert-notification {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            animation: slideIn 0.4s ease;
        }
        
        .alert-icon {
            font-size: 24px;
            margin-right: 12px;
        }
        
        .alert-text {
            font-size: 14px;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-label {
            display: block;
            color: #999999;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.3);
            background-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.05);
        }
        
        .form-input::placeholder {
            color: #666666;
        }
        
        .register-btn {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background: #ffffff;
            color: #000000;
            border: 2px solid #ffffff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .register-btn:hover {
            background: #000000;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
        }
        
        .register-footer {
            text-align: center;
            margin-top: 25px;
            color: #999999;
            font-size: 14px;
        }
        
        .register-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .register-footer a:hover {
            color: #cccccc;
            text-decoration: underline;
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
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <img src="../assets/logo.png" alt="N4P Logo" class="register-logo">
                <div class="register-title">N4P</div>
                <div class="register-subtitle">Buat akun baru Anda</div>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="alert-notification">
                    <span class="alert-icon">❌</span>
                    <span class="alert-text"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Pilih username Anda" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="Masukkan email Anda" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Minimal 6 karakter" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" class="form-input" placeholder="Ulangi password Anda" required>
                </div>
                
                <button type="submit" name="register" class="register-btn">✓ Daftar</button>
            </form>
            
            <div class="register-footer">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
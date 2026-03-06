<?php
session_start();
include '../config/database.php';

$error = '';

if(isset($_POST['login'])){
    // Validasi input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)){
        $error = "Username dan password tidak boleh kosong!";
    } else {
        // Gunakan prepared statement untuk mencegah SQL injection
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if($user && password_verify($password, $user['password'])){
            $_SESSION['user'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            $error = "Username atau Password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - N4P</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
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
        
        .login-container {
            width: 100%;
            max-width: 450px;
            animation: slideInUp 0.6s ease;
        }
        
        .login-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-logo {
            display: none;
        }
        
        .login-title {
            font-size: 72px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 30px;
            letter-spacing: 4px;
            font-family: 'Space Mono', monospace;
            text-transform: uppercase;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .login-subtitle {
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
        
        .alert-notification.hide {
            animation: slideOut 0.4s ease;
            opacity: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
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
        
        .login-btn {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
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
        
        .login-btn:hover {
            background: #000000;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            color: #999999;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .login-footer a:hover {
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
        
        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../assets/logo.png" alt="N4P Logo" class="login-logo">
                <div class="login-title">N4P</div>
                <div class="login-subtitle">Masuk ke akun Anda</div>
            </div>            
            <?php if(!empty($error)): ?>
                <div class="alert-notification" id="errorAlert">
                    <span class="alert-icon">❌</span>
                    <span class="alert-text"><?php echo $error; ?></span>
                </div>
                <script>
                    setTimeout(function() {
                        var alert = document.getElementById('errorAlert');
                        if (alert) {
                            alert.classList.add('hide');
                            setTimeout(function() {
                                alert.style.display = 'none';
                            }, 400);
                        }
                    }, 3000);
                </script>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Masukkan username Anda" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Masukkan password Anda" required>
                </div>
                
                <button type="submit" name="login" class="login-btn">🔓 Login</button>
            </form>
            
            <div class="login-footer">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
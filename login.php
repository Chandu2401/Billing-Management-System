<?php
session_start();
require_once 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        $query = "SELECT * FROM admin WHERE username = '$username' OR email = '$username' LIMIT 1";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_email'] = $admin['email'];
                
                // Update last login
                $conn->query("UPDATE admin SET last_login = NOW() WHERE id = " . $admin['id']);
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS (Google Font + tokens + login styles) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-blob teal"></div>
    <div class="login-blob amber"></div>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon"><i class="fas fa-receipt"></i></div>
            <h3><?php echo APP_NAME; ?></h3>
            <span class="login-eyebrow">Secure Admin Access</span>
        </div>

        <div class="receipt-tear"></div>

        <div class="login-body">
            <?php if ($error): ?>
                <div class="login-alert" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="field-group">
                    <i class="fas fa-user field-icon"></i>
                    <input type="text" id="username" name="username" placeholder=" " required autofocus>
                    <label for="username">Username or Email</label>
                </div>

                <div class="field-group">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Password</label>
                    <button type="button" class="field-toggle" onclick="toggleLoginPassword()" aria-label="Show password">
                        <i class="fas fa-eye" id="loginPwToggleIcon"></i>
                    </button>
                </div>

                <div class="login-remember">
                    <input type="checkbox" id="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" name="login" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>

            <div class="login-hint">
                <strong>Default credentials</strong><br>
                Username: admin &nbsp;·&nbsp; Password: admin123
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleLoginPassword() {
            const pw = document.getElementById('password');
            const icon = document.getElementById('loginPwToggleIcon');
            const isHidden = pw.type === 'password';
            pw.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>
</html>
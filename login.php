<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-btn {
            background: #147efb;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-btn:hover {
            background: #0056b3;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <?php
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    session_start();
    
    // If already logged in, redirect to admin.php
    if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header("Location: admin.php");
        exit();
    }

    // Check for cookie
    if(isset($_COOKIE['portfolio_username'])) {
        $saved_username = $_COOKIE['portfolio_username'];
    } else {
        $saved_username = "";
    }

    $error = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $servername = "localhost";
        $dbusername = "root";
        $dbpassword = "";
        $dbname = "portfolio";

        try {
            // Create connection
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $username = $_POST['username'];
            $password = $_POST['password'];
            $remember = isset($_POST['remember']) ? true : false;
            
            // Simple direct comparison without hashing
            $stmt = $conn->prepare("SELECT * FROM portfolio.admin WHERE username = ? AND password = ?");
            $stmt->execute([$username, $password]);
            
            if ($stmt->rowCount() > 0) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['username'] = $username;
                
                // Set cookie if remember me is checked
                if($remember) {
                    setcookie('portfolio_username', $username, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                }
                
                header("Location: admin.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } catch(PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
        }
        
        // Close connection
        $conn = null;
    }
    ?>

    <div class="login-container">
        <h2 style="margin-bottom: 20px; text-align: center;">Admin Login</h2>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($saved_username); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me for 30 days</label>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <p style="margin-top: 20px; text-align: center;">
            <a href="index.php" style="color: #147efb;">Back to Portfolio</a>
        </p>
    </div>
</body>
</html>

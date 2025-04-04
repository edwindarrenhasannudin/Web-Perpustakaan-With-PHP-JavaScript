<?php
ob_start();
session_start();

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables
$error = '';
$success = '';
$email = '';

// Handle Password Reset Request
if (isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        try {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                // Generate unique token
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiration
                
                // Store token in database
                $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
                $stmt->execute([$token, $expires, $email]);
                
                // Generate reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
                
                // For demo purposes, we'll store the link in session
                $_SESSION['demo_reset_link'] = $reset_link;
                $_SESSION['demo_email'] = $email;
                
                // Set success message
                $success = 'Password reset link has been sent to your email.';
            } else {
                $error = 'No account found with that email address.';
            }
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = 'A system error occurred. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- SweetAlert2 for beautiful popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .forgot-password-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .forgot-password-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-login a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        .error-message {
            color: #ff3333;
            background-color: #ffebeb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
        
        .success-message {
            color: #009900;
            background-color: #e6ffe6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            
            <button type="submit" name="reset_request" class="btn">Send Reset Link</button>
        </form>
        
        <div class="back-to-login">
            <a href="auth.php">Back to Login</a>
        </div>
    </div>

    <?php if (!empty($success) && isset($_SESSION['demo_reset_link'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Password Reset Link Sent!',
                html: `For demonstration purposes, here's your reset link:<br><br>
                      <a href="<?php echo $_SESSION['demo_reset_link']; ?>"><?php echo $_SESSION['demo_reset_link']; ?></a><br><br>
                      In a real application, this would be sent to <strong><?php echo $_SESSION['demo_email']; ?></strong>.`,
                icon: 'success',
                confirmButtonText: 'OK'
            });
            
            <?php unset($_SESSION['demo_reset_link']); ?>
            <?php unset($_SESSION['demo_email']); ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>
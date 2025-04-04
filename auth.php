<?php
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
$login_error = $register_error = '';
$register_success = '';
$name = $email = '';

// Handle Login
if (isset($_POST['login'])) {
    $email = trim($_POST['login_email']);
    $password = trim($_POST['login_password']);
    
    if (empty($email) || empty($password)) {
        $login_error = 'Both email and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: dashboard.php');
            exit();
        } else {
            $login_error = 'Invalid email or password.';
        }
    }
}

// Handle Registration
function handleRegistration($conn) {
    if (!isset($_POST['register'])) {
        return ['error' => null, 'success' => null];
    }

    // Validate inputs
    $name = trim($_POST['register_name'] ?? '');
    $email = trim($_POST['register_email'] ?? '');
    $password = trim($_POST['register_password'] ?? '');
    $confirm_password = trim($_POST['register_confirm_password'] ?? '');

    // Validation checks
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        return ['error' => 'All fields are required.', 'success' => null];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Invalid email format.', 'success' => null];
    }

    if ($password !== $confirm_password) {
        return ['error' => 'Passwords do not match.', 'success' => null];
    }

    if (strlen($password) < 6) {
        return ['error' => 'Password must be at least 6 characters long.', 'success' => null];
    }

    try {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return ['error' => 'Email already registered.', 'success' => null];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        if ($stmt->execute()) {
            return ['error' => null, 'success' => 'Registration successful! You can now login.'];
        } else {
            return ['error' => 'Registration failed. Please try again.', 'success' => null];
        }
        
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return ['error' => 'A system error occurred. Please try again later.', 'success' => null];
    }
}

// Invoke the registration handler
$registration_result = handleRegistration($conn);

// Determine which form to show
$show_login = true;
if (isset($_GET['form'])) {
    $show_login = ($_GET['form'] === 'login');
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="CSS/styles-login.css" />
    <title>Login & Registration Form</title>
    <style>
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
    <!-- Background Slideshow -->
    <div class="background-slideshow">
        <img src="img/perpustakaan.jpg" alt="Background 1" class="active">
        <img src="img/perpustakaan1.jpg" alt="Background 2">
        <img src="img/perpustakaan2.jpg" alt="Background 3">
    </div>

    <div class="container">
      <div class="forms">
        <!-- Login Form -->
        <div class="form login" <?php if (!$show_login) echo 'style="display: none;"'; ?>>
          <span class="title">Login</span>
          
          <?php if (!empty($login_error)): ?>
              <div class="error-message"><?php echo $login_error; ?></div>
          <?php endif; ?>
          
          <form action="auth.php?form=login" method="POST">
            <div class="input-field">
              <input type="text" name="login_email" placeholder="Enter your email" required 
                     value="<?php echo isset($_POST['login_email']) ? htmlspecialchars($_POST['login_email']) : ''; ?>" />
              <i class="uil uil-envelope icon"></i>
            </div>
            <div class="input-field">
              <input type="password" name="login_password" class="password" placeholder="Enter your password" required />
              <i class="uil uil-lock icon"></i>
              <i class="uil uil-eye-slash showHidePw"></i>
            </div>
            <div class="checkbox-text">
              <div class="checkbox-content">
                <input type="checkbox" id="logCheck" name="remember" />
                <label for="logCheck" class="text">Remember me</label>
              </div>
              <a href="forgot_password.php" class="text">Forgot password?</a>
            </div>
            <div class="input-field button">
              <input type="submit" name="login" value="Login" />
            </div>
          </form>
          <div class="login-signup">
            <span class="text"
              >Not a member?
              <a href="auth.php?form=register" class="text signup-link">Signup Now</a>
            </span>
          </div>
          <div class="back">
            <a href="index.html" class="text">Back To Home</a>
          </div>
        </div>
        
        <!-- Registration Form -->
        <div class="form signup" <?= $show_login ? 'style="display: none;"' : '' ?>>
          <span class="title">Registration</span>

          <?php if (isset($registration_result['error'])): ?>
              <div class="error-message"><?= htmlspecialchars($registration_result['error']) ?></div>
          <?php endif; ?>
          
          <?php if (isset($registration_result['success'])): ?>
              <div class="success-message"><?= htmlspecialchars($registration_result['success']) ?></div>
              <?php
              // Clear form values after successful registration
              $_POST['register_name'] = '';
              $_POST['register_email'] = '';
              ?>
          <?php endif; ?>
          
          <form action="auth.php?form=login" method="POST" autocomplete="off">
            <div class="input-field">
              <input type="text" name="register_name" placeholder="Enter your name" required 
                     value="<?php echo isset($_POST['register_name']) ? htmlspecialchars($_POST['register_name']) : ''; ?>" />
              <i class="uil uil-user"></i>
            </div>
            <div class="input-field">
              <input type="text" name="register_email" placeholder="Enter your email" required 
                     value="<?php echo isset($_POST['register_email']) ? htmlspecialchars($_POST['register_email']) : ''; ?>" />
              <i class="uil uil-envelope icon"></i>
            </div>
            <div class="input-field">
              <input type="password" name="register_password" class="password" placeholder="Create a password" required />
              <i class="uil uil-lock icon"></i>
              <i class="uil uil-eye-slash showHidePw"></i>
            </div>
            <div class="input-field">
              <input type="password" name="register_confirm_password" class="password" placeholder="Confirm a password" required />
              <i class="uil uil-lock icon"></i>
              <i class="uil uil-eye-slash showHidePw"></i>
            </div>
            <div class="checkbox-text">
              <div class="checkbox-content">
                <input type="checkbox" id="termCon" required />
                <label for="termCon" class="text">I accepted all terms and conditions</label>
              </div>
            </div>
            <div class="input-field button">
              <input type="submit" name="register" value="Signup" />
            </div>
          </form>
          <div class="login-signup">
            <span class="text"
              >Already a member?
              <a href="auth.php?form=login" class="text login-link">Login Now</a>
            </span>
          </div>
        </div>
      </div>
    </div>
    <script>
      // Toggle between forms
      document.querySelectorAll('.signup-link, .login-link').forEach(link => {
          link.addEventListener('click', (e) => {
              e.preventDefault();
              const formType = new URL(link.href).searchParams.get('form');
              document.querySelector('.login').style.display = formType === 'login' ? 'block' : 'none';
              document.querySelector('.signup').style.display = formType === 'register' ? 'block' : 'none';
              // Update URL without reload
              history.pushState(null, null, `auth.php?form=${formType}`);
          });
      });
    </script>
    <script src="script.js"></script>
  </body>
</html>
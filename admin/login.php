<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If directly accessing login page, destroy any existing session
// This forces a clean login every time
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Only destroy if not posting login form
    // Save redirect URL before destroying session if it exists
    $redirect_after_login = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : null;
    
    // Destroy session
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // Start a new session
    session_start();
    
    // Restore redirect URL if it existed
    if ($redirect_after_login) {
        $_SESSION['redirect_after_login'] = $redirect_after_login;
    }
}

// Check if user is already logged in, redirect to dashboard
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_role'])) {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once 'config/db.php';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Both username and password are required";
    } else {
        // Query database for user
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['last_regeneration'] = time(); // For session security
            
            // Redirect to the originally requested page or default to index.php
            $redirect_to = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
            unset($_SESSION['redirect_after_login']); // Clear the stored redirect
            header('Location: ' . $redirect_to);
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}

// Check for logout
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Avenza Avenue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transform: translateY(0);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .login-container:hover {
            transform: translateY(-5px);
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1e3c72, #2a5298, #1e3c72);
            background-size: 200% 100%;
            animation: gradientMove 3s linear infinite;
        }
        @keyframes gradientMove {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .login-logo h1 {
            color: #1e3c72;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .login-logo p {
            color: #666;
            font-size: 1rem;
            opacity: 0.8;
        }
        .form-floating {
            margin-bottom: 1.25rem;
        }
        .form-floating > .form-control {
            padding: 1.2rem 0.75rem;
            border: 2px solid #e1e5ee;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .form-floating > .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 4px rgba(30,60,114,0.1);
        }
        .form-floating > label {
            padding: 1.2rem 0.75rem;
            color: #666;
        }
        .btn-primary {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #2a5298, #1e3c72);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30,60,114,0.2);
        }
        .btn-primary:hover::after {
            left: 100%;
        }
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3c72;
            opacity: 0.5;
            transition: all 0.3s ease;
        }
        .form-floating > .form-control:focus ~ .input-icon {
            opacity: 1;
        }
        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .signup-link a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .signup-link a:hover {
            color: #2a5298;
            text-decoration: underline;
        }
        .remember-me {
            margin-bottom: 1rem;
        }
        .remember-me label {
            color: #666;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <h1>Avenza Avenue</h1>
                <p>Admin Portal</p>
            </div>
            <form id="loginForm">
                <div class="form-floating mb-3 position-relative">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username">Username</label>
                    <i class="bi bi-person input-icon"></i>
                </div>
                <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <i class="bi bi-lock input-icon"></i>
                </div>
                <div class="remember-me form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
                <div class="signup-link">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </div>
            </form>
        </div>
        <div class="login-footer">
            © 2024 Avenza Avenue. All rights reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred. Please try again.'
            });
        });
    });

    // Show success message if redirected after registration
    <?php if(isset($_SESSION['registration_success'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Registration Successful!',
        text: 'Please login with your credentials',
        timer: 2000,
        showConfirmButton: false
    });
    <?php unset($_SESSION['registration_success']); endif; ?>
    </script>
</body>
</html> 
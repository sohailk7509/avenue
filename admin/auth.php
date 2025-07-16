<?php
/**
 * Admin Authentication System
 * This file should be included at the top of all admin pages
 * It prevents unauthorized access by redirecting to the login page
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define the login page path
$login_page = 'login.php';

// Check if user is on the login page or auth page itself
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = [$login_page, 'auth.php', 'register.php'];

// If current page is login.php, don't redirect (even if there's a session)
// This allows the login page to handle session destruction properly
if ($current_page === $login_page) {
    return; // Exit auth check early, let login.php handle sessions
}

// If not on a public page and not logged in, redirect to login
if (!in_array($current_page, $public_pages) && !isset($_SESSION['admin_logged_in'])) {
    // Store the requested page for redirecting after login (optional)
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    header("Location: $login_page");
    exit();
}

// Security measures to prevent session hijacking
function regenerateSession() {
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration']) || 
        (time() - $_SESSION['last_regeneration']) > 1800) { // 30 minutes
        
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// If user is logged in, regenerate session periodically
if (isset($_SESSION['admin_logged_in'])) {
    regenerateSession();
}

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('success' => false, 'message' => '');
    
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;

        // Validate inputs
        if (empty($username) || empty($password)) {
            throw new Exception('Please enter both username and password');
        }

        // Get user from database
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $pdo->query($query);
        $user = $result->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_name'] = $user['firstname'] . ' ' . $user['lastname'];

            // Set remember me cookie if checked
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                
                // Store token in database
                $query = "UPDATE users SET remember_token = '$token' WHERE id = " . $user['id'];
                $pdo->query($query);
            }

            $response['success'] = true;
            $response['message'] = 'Login successful! Redirecting...';
        } else {
            throw new Exception('Invalid username or password');
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?> 
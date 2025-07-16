<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('success' => false, 'message' => '');
    
    try {
        // Get and sanitize form data
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'];

        // Validate empty fields
        if (empty($firstname) || empty($lastname) || empty($email) || 
            empty($username) || empty($password) || empty($role)) {
            throw new Exception("All fields are required");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check password match
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match");
        }

        // Validate password strength
        if (strlen($password) < 8 || 
            !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || 
            !preg_match('/[0-9]/', $password) || 
            !preg_match('/[!@#$%^&*]/', $password)) {
            throw new Exception("Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters");
        }

        // Check if email exists
        $check_email = $pdo->query("SELECT COUNT(*) FROM users WHERE email = '$email'")->fetchColumn();
        if ($check_email > 0) {
            throw new Exception("Email already registered");
        }

        // Check if username exists
        $check_username = $pdo->query("SELECT COUNT(*) FROM users WHERE username = '$username'")->fetchColumn();
        if ($check_username > 0) {
            throw new Exception("Username already taken");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $query = "INSERT INTO users (firstname, lastname, email, username, password, role) 
                 VALUES ('$firstname', '$lastname', '$email', '$username', '$hashed_password', '$role')";
        
        if ($pdo->query($query)) {
            $response['success'] = true;
            $response['message'] = "Registration successful! Redirecting to login...";
            $_SESSION['registration_success'] = true;
        } else {
            throw new Exception("Registration failed. Please try again.");
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
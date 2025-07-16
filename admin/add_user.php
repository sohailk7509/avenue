<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validate input
    if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
        throw new Exception("All fields are required");
    }

    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert user (removed phone and address)
    $query = "INSERT INTO users (firstname, lastname, username, email, password, role, status) 
             VALUES (?, ?, ?, ?, ?, ?, 'active')";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([
        $data['firstname'],
        $data['lastname'],
        $data['username'],
        $data['email'],
        $hashedPassword,
        $data['role']
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        throw new Exception("Failed to add user");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 
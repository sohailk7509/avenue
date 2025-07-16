<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    if (empty($data['username']) || empty($data['email']) || empty($data['role']) || empty($data['status'])) {
        throw new Exception("Required fields cannot be empty");
    }

    // Set default values if not provided
    $data['firstname'] = $data['firstname'] ?? '';
    $data['lastname'] = $data['lastname'] ?? '';

    if (!empty($data['password'])) {
        // Update with new password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['username'],
            $data['email'],
            $hashedPassword,
            $data['role'],
            $data['status'],
            $data['id']
        ]);
    } else {
        // Update without changing password
        $query = "UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, role = ?, status = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['username'],
            $data['email'],
            $data['role'],
            $data['status'],
            $data['id']
        ]);
    }

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        throw new Exception("Failed to update user");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 
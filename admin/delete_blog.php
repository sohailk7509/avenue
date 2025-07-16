<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $query = "DELETE FROM blogs WHERE id = ? AND admin_id = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$data['id'], $_SESSION['admin_id']]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete blog']);
    }
}
?> 
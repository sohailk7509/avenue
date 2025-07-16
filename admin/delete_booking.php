<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $query = "DELETE FROM bookings WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$data['id']]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}
?> 
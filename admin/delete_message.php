<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    if (empty($data['id'])) {
        throw new Exception("Message ID is required");
    }

    $query = "DELETE FROM messages WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$data['id']]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
    } else {
        throw new Exception("Failed to delete message");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 
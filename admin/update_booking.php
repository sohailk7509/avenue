<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['status'])) {
    try {
        // Debug
        error_log("Updating booking: " . print_r($data, true));
        
        $query = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$data['status'], $data['id']]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            throw new Exception("Failed to update status");
        }
    } catch (Exception $e) {
        error_log("Error updating status: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
}
?> 
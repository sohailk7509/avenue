<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
            throw new Exception("All fields are required");
        }

        // Insert message
        $query = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['subject'] ?? '',
            $_POST['message']
        ]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
        } else {
            throw new Exception("Failed to send message");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
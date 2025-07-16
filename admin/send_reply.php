<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    if (empty($data['id']) || empty($data['reply'])) {
        throw new Exception("Message ID and reply are required");
    }

    // Update message with reply
    $query = "UPDATE messages SET 
              replied = TRUE, 
              reply_message = ?, 
              reply_date = CURRENT_TIMESTAMP,
              status = 'read'
              WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$data['reply'], $data['id']]);

    if ($result) {
        // Get message details for email
        $getMsg = "SELECT * FROM messages WHERE id = ?";
        $stmt = $pdo->prepare($getMsg);
        $stmt->execute([$data['id']]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send email to user (you can customize this part)
        $to = $message['email'];
        $subject = "Re: " . ($message['subject'] ?? 'Your Message');
        $headers = "From: your-email@domain.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($to, $subject, $data['reply'], $headers);

        echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);
    } else {
        throw new Exception("Failed to send reply");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 
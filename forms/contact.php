<?php
// Database connection
require_once '../admin/config/db.php';

// Initialize JSON response
$response = array(
    'success' => false,
    'message' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get and sanitize form data
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $status = 'unread'; // Default status for new messages

        // Validate required fields
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            throw new Exception("Please fill all required fields.");
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        // Check for duplicate message from the same email within the last 5 minutes
        $checkQuery = "SELECT COUNT(*) FROM messages WHERE email = '$email' AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
        $checkResult = $pdo->query($checkQuery);
        $count = $checkResult->fetchColumn();
        
        if ($count > 0) {
            $response['success'] = true; // Still return success to prevent confusion
            $response['message'] = 'Your message was already received. We will contact you soon!';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Execute SQL query directly
        $insertQuery = "INSERT INTO messages (name, email, subject, message, status, created_at) 
                        VALUES ('$name', '$email', '$subject', '$message', '$status', NOW())";
        
        if ($pdo->query($insertQuery)) {
            $response['success'] = true;
            $response['message'] = 'Your message has been sent. Thank you!';
        } else {
            throw new Exception("Failed to send message. Please try again.");
        }
    } catch (PDOException $e) {
        $response['message'] = "Database error: " . $e->getMessage();
        error_log("PDO Error: " . $e->getMessage());
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Form Error: " . $e->getMessage());
    }

} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit; 
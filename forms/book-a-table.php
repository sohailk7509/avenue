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
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $unit_type = filter_input(INPUT_POST, 'unit_type', FILTER_SANITIZE_STRING);
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING) ?? '';
        $status = 'pending'; // Default status for new bookings

        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($unit_type)) {
            throw new Exception("Please fill all required fields.");
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        // Check for duplicate entry from the same email within the last 5 minutes
        $checkQuery = "SELECT COUNT(*) FROM bookings WHERE email = '$email' AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
        $checkResult = $pdo->query($checkQuery);
        $count = $checkResult->fetchColumn();
        
        if ($count > 0) {
            $response['success'] = true; // Still return success to prevent confusion
            $response['message'] = 'Your booking request was already received. We will contact you soon!';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Execute SQL query directly
        $insertQuery = "INSERT INTO bookings (firstname, email, phone, booking_date, unit_type, notes, status, created_at) 
                        VALUES ('$name', '$email', '$phone', '$date', '$unit_type', '$notes', '$status', NOW())";
        
        if ($pdo->query($insertQuery)) {
            $response['success'] = true;
            $response['message'] = 'Your booking request has been sent. We will call or email you to confirm your reservation. Thank you!';
        } else {
            throw new Exception("Failed to submit booking. Please try again.");
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

<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('success' => false, 'message' => '');
    
    try {
        // Get and sanitize form data
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $unit_type = $_POST['unit_type'];
        $unit_size = $_POST['unit_size'];
        $floor = $_POST['floor'];
        $booking_date = $_POST['booking_date'];
        $status = 'pending'; // Default status for new bookings
        $notes = $_POST['notes'] ?? '';

        // Insert booking into database
        $query = "INSERT INTO bookings (firstname, lastname, email, phone, unit_type, unit_size, floor, booking_date, status, notes) 
                 VALUES ('$firstname', '$lastname', '$email', '$phone', '$unit_type', '$unit_size', '$floor', '$booking_date', '$status', '$notes')";
        
        if ($pdo->query($query)) {
            $response['success'] = true;
            $response['message'] = 'Your booking has been submitted successfully!';
        } else {
            throw new Exception("Failed to submit booking. Please try again.");
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?> 
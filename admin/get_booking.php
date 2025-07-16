<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM bookings WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($booking);
}
?> 
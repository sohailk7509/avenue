<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit('Unauthorized');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT id, firstname, lastname, username, email, role, status FROM users WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $user['firstname'] = $user['firstname'] ?? '';
    $user['lastname'] = $user['lastname'] ?? '';
    
    header('Content-Type: application/json');
    echo json_encode($user);
}
?> 
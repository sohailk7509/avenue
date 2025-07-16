<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $query = "SELECT * FROM blogs WHERE id = ? AND admin_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['admin_id']]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($blog) {
        echo json_encode($blog);
    } else {
        echo json_encode(['success' => false, 'message' => 'Blog not found']);
    }
}
?> 
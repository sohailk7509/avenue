<?php
// Add these at the very top to prevent PHP errors from being displayed
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    if (empty($_POST['title']) || empty($_POST['content'])) {
        throw new Exception("Title and content are required");
    }

    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Generate unique filename
        $image_name = uniqid() . "." . $ext;
        $upload_path = "../uploads/blogs/";
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_path)) {
            if (!@mkdir($upload_path, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }
        
        // Check directory permissions
        if (!is_writable($upload_path)) {
            throw new Exception("Upload directory is not writable");
        }
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $image_name)) {
            throw new Exception("Failed to upload image");
        }
    }

    // Insert blog post
    $query = "INSERT INTO blogs (admin_id, title, content, image, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([
        $_SESSION['admin_id'],
        $_POST['title'],
        $_POST['content'],
        $image_name,
        $_POST['status']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to save blog");
    }
} catch (Exception $e) {
    error_log("Blog save error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Error $e) {
    error_log("Unexpected error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
?> 
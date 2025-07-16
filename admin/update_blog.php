<?php
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
    if (empty($_POST['blog_id']) || empty($_POST['title']) || empty($_POST['content'])) {
        throw new Exception("Required fields are missing");
    }

    // Verify blog ownership
    $query = "SELECT * FROM blogs WHERE id = ? AND admin_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_POST['blog_id'], $_SESSION['admin_id']]);
    $blog = $stmt->fetch();

    if (!$blog) {
        throw new Exception("Blog not found or unauthorized");
    }

    $image_name = $blog['image']; // Keep existing image by default

    // Handle new image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.");
        }

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

        // Delete old image if exists
        if ($blog['image'] && file_exists($upload_path . $blog['image'])) {
            unlink($upload_path . $blog['image']);
        }
    }

    $query = "UPDATE blogs SET title = ?, content = ?, image = ?, status = ? WHERE id = ? AND admin_id = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([
        $_POST['title'],
        $_POST['content'],
        $image_name,
        $_POST['status'],
        $_POST['blog_id'],
        $_SESSION['admin_id']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
        exit();
    } else {
        throw new Exception("Failed to update blog");
    }
} catch (Exception $e) {
    error_log("Blog update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
} catch (Error $e) {
    error_log("Unexpected error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
    exit();
}
?> 
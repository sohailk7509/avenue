<?php
// Set header to return JSON
header('Content-Type: application/json');

// Check if blog ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Blog ID is required'
    ]);
    exit();
}

$blog_id = intval($_GET['id']);

try {
    // Connect to database
    require_once 'admin/config/db.php';
    
    // Get the blog post details
    $query = "SELECT b.*, a.username as author_name 
              FROM blogs b 
              LEFT JOIN users a ON b.admin_id = a.id 
              WHERE b.id = ? AND b.status = 'published'";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$blog_id]);
    
    if ($stmt->rowCount() == 0) {
        // Blog not found or not published
        echo json_encode([
            'success' => false,
            'message' => 'Blog post not found'
        ]);
        exit();
    }
    
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Format dates
    $formatted_date = date('F d, Y', strtotime($blog['created_at']));
    $image_url = $blog['image'] ? 'uploads/blogs/' . $blog['image'] : 'assets/img/events-slider/events-slider-1.jpg';
    
    // Prepare response
    $response = [
        'success' => true,
        'id' => $blog['id'],
        'title' => $blog['title'],
        'content' => $blog['content'],
        'image' => $image_url,
        'author_name' => $blog['author_name'] ?? 'Admin',
        'created_at' => $blog['created_at'],
        'formatted_date' => $formatted_date
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Log error and return error response
    error_log("Blog details error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?> 
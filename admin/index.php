<?php
// Include the authentication system
require_once 'auth.php';

// Already has session_start and auth check, so those can be removed
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Avenza Avenue</title>
    
    <!-- Bootstrap CSS and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 for nice alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            padding: 80px 20px 20px;
            transition: all 0.3s ease;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 7px 15px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 10px;
            margin-bottom: 15px;
            background: rgba(30, 144, 255, 0.1);
            color: #1e90ff;
        }
        
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .card-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding-top: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid px-4">
                <h1 class="mt-4 mb-4">Dashboard</h1>
                
                <div class="row g-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-calendar-check fs-4"></i>
                                </div>
                                <h5 class="card-title">Total Bookings</h5>
                                <?php
                                // Get booking count
                                try {
                                    $bookingQuery = "SELECT COUNT(*) FROM bookings";
                                    $bookingResult = $pdo->query($bookingQuery);
                                    $bookingCount = $bookingResult->fetchColumn();
                                } catch (PDOException $e) {
                                    $bookingCount = 0;
                                }
                                ?>
                                <p class="card-text"><?php echo $bookingCount; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="stat-icon" style="background: rgba(255, 99, 71, 0.1); color: tomato;">
                                    <i class="bi bi-envelope fs-4"></i>
                                </div>
                                <h5 class="card-title">Messages</h5>
                                <?php
                                // Get messages count
                                try {
                                    $messageQuery = "SELECT COUNT(*) FROM messages";
                                    $messageResult = $pdo->query($messageQuery);
                                    $messageCount = $messageResult->fetchColumn();
                                } catch (PDOException $e) {
                                    $messageCount = 0;
                                }
                                ?>
                                <p class="card-text"><?php echo $messageCount; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="stat-icon" style="background: rgba(75, 192, 192, 0.1); color: #4bc0c0;">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <h5 class="card-title">Users</h5>
                                <?php
                                // Get users count
                                try {
                                    $userQuery = "SELECT COUNT(*) FROM users";
                                    $userResult = $pdo->query($userQuery);
                                    $userCount = $userResult->fetchColumn();
                                } catch (PDOException $e) {
                                    $userCount = 0;
                                }
                                ?>
                                <p class="card-text"><?php echo $userCount; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="stat-icon" style="background: rgba(153, 102, 255, 0.1); color: #9966ff;">
                                    <i class="bi bi-file-text fs-4"></i>
                                </div>
                                <h5 class="card-title">Blog Posts</h5>
                                <?php
                                // Get blog count
                                try {
                                    $blogQuery = "SELECT COUNT(*) FROM blogs";
                                    $blogResult = $pdo->query($blogQuery);
                                    $blogCount = $blogResult->fetchColumn();
                                } catch (PDOException $e) {
                                    $blogCount = 0;
                                }
                                ?>
                                <p class="card-text"><?php echo $blogCount; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Unit</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Get recent bookings
                                            try {
                                                $recentBookingsQuery = "SELECT * FROM bookings ORDER BY id DESC LIMIT 5";
                                                $recentBookingsResult = $pdo->query($recentBookingsQuery);
                                                
                                                if ($recentBookingsResult && $recentBookingsResult->rowCount() > 0) {
                                                    while ($booking = $recentBookingsResult->fetch(PDO::FETCH_ASSOC)) {
                                                        $statusClass = '';
                                                        switch ($booking['status']) {
                                                            case 'approved':
                                                                $statusClass = 'bg-success';
                                                                break;
                                                            case 'rejected':
                                                                $statusClass = 'bg-danger';
                                                                break;
                                                            default:
                                                                $statusClass = 'bg-warning';
                                                        }
                                                        
                                                        echo '<tr>';
                                                        if (isset($booking['name'])) {
                                                            echo '<td>' . htmlspecialchars($booking['name']) . '</td>';
                                                        } else {
                                                            echo '<td>' . htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']) . '</td>';
                                                        }
                                                        echo '<td>' . date('d M Y', strtotime($booking['booking_date'])) . '</td>';
                                                        echo '<td>' . htmlspecialchars($booking['unit_type']) . '</td>';
                                                        echo '<td><span class="badge ' . $statusClass . '">' . ucfirst($booking['status']) . '</span></td>';
                                                        echo '</tr>';
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="4" class="text-center">No bookings found</td></tr>';
                                                }
                                            } catch (PDOException $e) {
                                                echo '<tr><td colspan="4" class="text-center">Error retrieving bookings</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="bookings.php" class="btn btn-sm btn-primary">View All Bookings</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Messages</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get recent messages
                                try {
                                    $recentMessagesQuery = "SELECT * FROM messages ORDER BY id DESC LIMIT 3";
                                    $recentMessagesResult = $pdo->query($recentMessagesQuery);
                                    
                                    if ($recentMessagesResult && $recentMessagesResult->rowCount() > 0) {
                                        while ($message = $recentMessagesResult->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<div class="d-flex mb-3 p-3 bg-light rounded">';
                                            echo '<div class="flex-shrink-0">';
                                            echo '<i class="bi bi-person-circle fs-4"></i>';
                                            echo '</div>';
                                            echo '<div class="ms-3">';
                                            echo '<h6 class="mb-1">' . htmlspecialchars($message['name']) . '</h6>';
                                            echo '<p class="mb-1 text-muted small">' . htmlspecialchars($message['subject']) . '</p>';
                                            echo '<p class="mb-0 small">' . substr(htmlspecialchars($message['message']), 0, 60) . '...</p>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        echo '<a href="messages.php" class="btn btn-sm btn-primary">View All Messages</a>';
                                    } else {
                                        echo '<p class="text-center">No messages found</p>';
                                    }
                                } catch (PDOException $e) {
                                    echo '<p class="text-center">Error retrieving messages</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
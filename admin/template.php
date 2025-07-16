<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Avenza Avenue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="row content left-menu-space main-content">
        <div class="col-sm-12" id="">
            <!-- Top Navigation -->
            <div class="top-nav">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link sidebar-toggle d-md-none me-3">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <h4 class="mb-0">Page Title</h4>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo $_SESSION['admin_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="content-card">
                <!-- Your Form Goes Here -->
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Field Label</label>
                            <input type="text" class="form-control">
                        </div>
                        <!-- Add more form fields as needed -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const overlay = document.querySelector('.sidebar-overlay');

        // Toggle sidebar on mobile
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 767.98) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });
    </script>
</body>
</html> 
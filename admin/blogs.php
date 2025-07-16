<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Records - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="row content left-menu-space main-content">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">My Blog Records</h3>
                    <button class="btn btn-primary" onclick="location.href='add_blog.php'">
                        <i class="bi bi-plus-lg"></i> Add New Blog
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="blogsTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Only fetch blogs created by current admin
                                $query = "SELECT * FROM blogs WHERE admin_id = ? ORDER BY created_at DESC";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute([$admin_id]);
                                
                                while ($blog = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $statusBadge = $blog['status'] == 'published' 
                                        ? '<span class="badge bg-success">Published</span>' 
                                        : '<span class="badge bg-warning">Draft</span>';
                                    
                                    echo "<tr>
                                        <td>{$blog['title']}</td>
                                        <td style='position: relative; right: 0px;'>{$statusBadge}</td>
                                        <td>" . date('M d, Y H:i', strtotime($blog['created_at'])) . "</td>
                                        <td>" . date('M d, Y H:i', strtotime($blog['updated_at'])) . "</td>
                                        <td>
                                            <button class='btn btn-sm btn-primary view-btn' data-id='{$blog['id']}'>
                                                <i class='bi bi-eye'></i>
                                            </button>
                                            <a href='edit_blog.php?id={$blog['id']}' class='btn btn-sm btn-info'>
                                                <i class='bi bi-pencil'></i>
                                            </a>
                                            <button class='btn btn-sm btn-danger delete-btn' data-id='{$blog['id']}'>
                                                <i class='bi bi-trash'></i>
                                            </button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#blogsTable').DataTable({
            order: [[2, 'desc']], // Sort by created date by default
            pageLength: 10,
            responsive: true
        });

        // View Blog
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            fetch('get_blog.php?id=' + id)
                .then(response => response.json())
                .then(blog => {
                    if (blog.success === false) {
                        Swal.fire('Error', blog.message, 'error');
                        return;
                    }
                    Swal.fire({
                        title: blog.title,
                        html: `
                            <div class="text-start">
                                ${blog.content}
                            </div>
                            ${blog.image ? `<img src="../uploads/blogs/${blog.image}" class="img-fluid mt-3">` : ''}
                        `,
                        width: '800px'
                    });
                });
        });

        // Delete Blog
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete Blog?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_blog.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Deleted!',
                                'Blog has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            error.message,
                            'error'
                        );
                    });
                }
            });
        });
    });
    </script>
</body>
</html> 
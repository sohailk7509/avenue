<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Now we can use $pdo
$check_query = "DESCRIBE bookings";
$check_result = $pdo->query($check_query);
error_log("Table structure: " . print_r($check_result->fetchAll(), true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .card {
            margin: 15px;
        }
        .main-content {
            padding: 20px;
            margin-left: 0;
        }
        @media (max-width: 768px) {
            .table td, .table th {
                min-width: 100px;
            }
            .table td:first-child, .table th:first-child {
                min-width: 50px;
            }
            .btn-sm {
                padding: .25rem .5rem;
            }
        }
        
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="row content left-menu-space main-content">
        <div class="col-sm-12">
            <div class="container-fluid">
                <main id="main" class="main">
                    <div class="pagetitle">
                        <h1>Bookings</h1>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Bookings</li>
                            </ol>
                        </nav>
                    </div>

                    <section class="section">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Bookings</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Date</th>
                                                <th>Unit Type</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // First, get total count of records
                                            $countQuery = "SELECT COUNT(*) as total FROM bookings";
                                            $countResult = $pdo->query($countQuery);
                                            $totalRows = $countResult->fetch(PDO::FETCH_ASSOC)['total'];
                                            
                                            // Then get all bookings
                                            $query = "SELECT *, COALESCE(status, 'pending') as status FROM bookings ORDER BY id DESC";
                                            $result = $pdo->query($query);
                                            
                                            $serial = $totalRows; // Start from total count
                                            
                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                // Debug
                                                error_log("Row data: " . print_r($row, true));
                                                
                                                // Get status class
                                                $status = $row['status'] ?? 'pending';
                                                $statusClass = match($status) {
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    default => 'bg-warning'
                                                };
                                                
                                                echo "<tr>";
                                                echo "<td>{$serial}</td>"; // Display descending serial number
                                                echo "<td>{$row['firstname']}</td>";
                                                echo "<td>{$row['email']}</td>";
                                                echo "<td>{$row['phone']}</td>";
                                                echo "<td>" . date('d M Y', strtotime($row['booking_date'])) . "</td>";
                                                echo "<td>{$row['unit_type']}</td>";
                                                echo "<td><span class='badge {$statusClass} px-3 py-2' style='position: relative;'>" . ucfirst($status) . "</span></td>";
                                                echo "<td>
                                                        <button class='btn btn-sm btn-primary view-btn' data-id='{$row['id']}'><i class='bi bi-eye'></i></button>
                                                        <button class='btn btn-sm btn-success approve-btn' data-id='{$row['id']}'><i class='bi bi-check-lg'></i></button>
                                                        <button class='btn btn-sm btn-warning reject-btn' data-id='{$row['id']}'><i class='bi bi-x-lg'></i></button>
                                                        <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'><i class='bi bi-trash'></i></button>
                                                    </td>";
                                                echo "</tr>";
                                                
                                                $serial--; // Decrement serial number
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // View Booking Details
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`get_booking.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: 'Booking Details',
                            html: `
                                <div class="text-start">
                                    <p><strong>Name:</strong> ${data.firstname} ${data.lastname}</p>
                                    <p><strong>Email:</strong> ${data.email}</p>
                                    <p><strong>Phone:</strong> ${data.phone}</p>
                                    <p><strong>Date:</strong> ${data.booking_date}</p>
                                    <p><strong>Unit Type:</strong> ${data.unit_type}</p>
                                    <p><strong>Unit Size:</strong> ${data.unit_size}</p>
                                    <p><strong>Floor:</strong> ${data.floor}</p>
                                    <p><strong>Notes:</strong> ${data.notes || 'No notes'}</p>
                                    <p><strong>Status:</strong> ${data.status}</p>
                                </div>
                            `,
                            confirmButtonText: 'Close'
                        });
                    });
            });
        });

        // Approve Booking
        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const row = this.closest('tr'); // Get the parent row
                
                Swal.fire({
                    title: 'Approve Booking?',
                    text: "This will mark the booking as approved",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('update_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: id,
                                status: 'approved'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the status badge in the table
                                const statusCell = row.querySelector('td:nth-child(7)');
                                statusCell.innerHTML = '<span class="badge bg-success">Approved</span>';
                                
                                Swal.fire(
                                    'Approved!',
                                    'Booking has been approved.',
                                    'success'
                                );
                            } else {
                                throw new Error(data.message || 'Failed to update status');
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

        // Delete Booking
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Delete Booking?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('delete_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Booking has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        });

        // Add Reject functionality
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const row = this.closest('tr');
                
                Swal.fire({
                    title: 'Reject Booking?',
                    text: "This will mark the booking as rejected",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, reject it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('update_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: id,
                                status: 'rejected'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the status badge in the table
                                const statusCell = row.querySelector('td:nth-child(7)');
                                statusCell.innerHTML = '<span class="badge bg-danger">Rejected</span>';
                                
                                Swal.fire(
                                    'Rejected!',
                                    'Booking has been rejected.',
                                    'success'
                                );
                            } else {
                                throw new Error(data.message || 'Failed to update status');
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
    });
    </script>
</body>
</html> 
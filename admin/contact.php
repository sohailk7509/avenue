<?php
session_start();
require_once 'config/db.php';

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
    <title>Messages - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .table-responsive {
            margin: 20px 0;
        }
        .unread {
            font-weight: 600;
            background-color: rgba(13, 110, 253, 0.05);
        }
        .message-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .badge-unread {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="row content left-menu-space main-content">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Messages</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="messagesTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM messages ORDER BY created_at DESC";
                                $result = $pdo->query($query);
                                
                                while ($message = $result->fetch(PDO::FETCH_ASSOC)) {
                                    $rowClass = $message['status'] == 'unread' ? 'unread' : '';
                                    $statusBadge = $message['status'] == 'unread' 
                                        ? '<span class="badge bg-danger">Unread</span>' 
                                        : '<span class="badge bg-success">Read</span>';
                                    
                                    echo "<tr class='{$rowClass}'>
                                        <td>{$message['name']}</td>
                                        <td>{$message['email']}</td>
                                        <td>{$message['subject']}</td>
                                        <td class='message-content'>{$message['message']}</td>
                                        <td>" . date('M d, Y H:i', strtotime($message['created_at'])) . "</td>
                                        <td>{$statusBadge}</td>
                                        <td>
                                            <button class='btn btn-sm btn-primary view-btn' data-id='{$message['id']}'>
                                                <i class='bi bi-eye'></i>
                                            </button>
                                            <button class='btn btn-sm btn-info reply-btn' data-id='{$message['id']}'>
                                                <i class='bi bi-reply'></i>
                                            </button>
                                            <button class='btn btn-sm btn-danger delete-btn' data-id='{$message['id']}'>
                                                <i class='bi bi-trash'></i>
                                            </button>
                                        </td>
                                    </tr>";
                                }

                                // Mark messages as read
                                $query = "UPDATE messages SET status = 'read' WHERE status = 'unread'";
                                $pdo->query($query);
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
        $('#messagesTable').DataTable({
            order: [[4, 'desc']], // Sort by date column by default
            pageLength: 10,
            responsive: true
        });

        // View Message
        $('.view-btn').click(function() {
            const row = $(this).closest('tr');
            Swal.fire({
                title: row.find('td:eq(2)').text(), // Subject
                html: `
                    <p><strong>From:</strong> ${row.find('td:eq(0)').text()} (${row.find('td:eq(1)').text()})</p>
                    <p><strong>Message:</strong></p>
                    <p>${row.find('td:eq(3)').text()}</p>
                `,
                width: '600px'
            });
        });

        // Delete Message
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete Message?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_message.php', {
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
                                'Message has been deleted.',
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

        // Reply to Message
        $('.reply-btn').click(function() {
            const id = $(this).data('id');
            const row = $(this).closest('tr');
            Swal.fire({
                title: 'Reply to Message',
                html: `
                    <div class="mb-3">
                        <p><strong>To:</strong> ${row.find('td:eq(0)').text()} (${row.find('td:eq(1)').text()})</p>
                        <p><strong>Subject:</strong> ${row.find('td:eq(2)').text()}</p>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="reply-message" rows="5" placeholder="Type your reply..."></textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Send Reply',
                width: '600px',
                preConfirm: () => {
                    return {
                        id: id,
                        reply: document.getElementById('reply-message').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('send_reply.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Sent!',
                                'Reply has been sent successfully.',
                                'success'
                            );
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
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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .message-card {
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .message-header {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 15px 15px 0 0;
            border-bottom: 1px solid #eee;
        }
        .message-body {
            padding: 20px;
        }
        .message-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
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
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Messages</h5>
                                
                                <div class="messages-container">
                                    <?php
                                    $query = "SELECT * FROM messages ORDER BY created_at DESC";
                                    $result = $pdo->query($query);
                                    
                                    while ($message = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $statusBadge = $message['status'] == 'unread' 
                                            ? '<span class="badge-unread">Unread</span>' 
                                            : '';
                                            
                                        echo "
                                        <div class='message-card'>
                                            <div class='message-header d-flex justify-content-between align-items-center'>
                                                <div>
                                                    <strong>{$message['name']}</strong>
                                                    <span class='text-muted ms-2'>{$message['email']}</span>
                                                </div>
                                                {$statusBadge}
                                            </div>
                                            <div class='message-body'>
                                                {$message['message']}
                                            </div>
                                            <div class='message-footer d-flex justify-content-between align-items-center'>
                                                <small class='text-muted'>
                                                    " . date('M d, Y H:i', strtotime($message['created_at'])) . "
                                                </small>
                                                <div>
                                                    <button class='btn btn-sm btn-primary reply-btn' data-id='{$message['id']}'>
                                                        <i class='bi bi-reply'></i> Reply
                                                    </button>
                                                    <button class='btn btn-sm btn-danger delete-btn' data-id='{$message['id']}'>
                                                        <i class='bi bi-trash'></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div></div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reply to message
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                // Add your reply functionality here
                Swal.fire({
                    title: 'Reply to Message',
                    html: `
                        <form>
                            <div class="form-group">
                                <textarea class="form-control" id="reply-message" rows="5" placeholder="Type your reply..."></textarea>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Send Reply',
                    preConfirm: () => {
                        return {
                            id: id,
                            reply: document.getElementById('reply-message').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send reply
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

        // Delete message
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
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
        });
    });
    </script>
</body>
</html> 
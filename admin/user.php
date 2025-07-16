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
    <title>Users - Admin Panel</title>
    
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
        .content-wrapper {
            margin-left: 250px; /* Adjust based on your sidebar width */
        }
        .content {
            padding: 20px;
        }
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3c72;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus ~ .input-icon {
            opacity: 1;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: -1rem;
            margin-bottom: 1rem;
            padding-left: 0.75rem;
        }

        .form-floating.role-select {
            position: relative;
        }
        
        .form-floating.role-select select.form-control {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
            padding-top: 1.625rem;
        }
        
        .form-floating.role-select label {
            opacity: 0.65;
            transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
        }
        
        .role-info {
            font-size: 0.85rem;
            color: #666;
            margin-top: -1rem;
            margin-bottom: 1rem;
            padding-left: 0.75rem;
        }

        .role-badge {
            position: absolute;
            right: 2.5rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .signup-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="row content left-menu-space main-content">
        <div class="col-sm-12">
    <!-- <div class="content-wrapper"> -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- <div class="col-12"> -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title">All Users</h5>
                                    <button class="btn btn-primary" onclick="addUser()">
                                        <i class="bi bi-plus-circle"></i> Add New User
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Get total count
                                            $countQuery = "SELECT COUNT(*) as total FROM users";
                                            $countResult = $pdo->query($countQuery);
                                            $totalRows = $countResult->fetch(PDO::FETCH_ASSOC)['total'];
                                            
                                            // Get all users
                                            $query = "SELECT * FROM users ORDER BY id DESC";
                                            $result = $pdo->query($query);
                                            
                                            $serial = $totalRows;
                                            
                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                // Format role display
                                                $roleClass = match($row['role']) {
                                                    'super_admin' => 'bg-danger',
                                                    'admin' => 'bg-success',
                                                    'manager' => 'bg-primary',
                                                    default => 'bg-secondary'
                                                };
                                                $roleDisplay = str_replace('_', ' ', ucwords($row['role']));
                                                
                                                // Status badge
                                                $status = $row['status'] ?? 'active';
                                                $statusClass = $status == 'active' ? 'bg-success' : 'bg-danger';
                                                
                                                echo "<tr>";
                                                echo "<td>{$serial}</td>";
                                                echo "<td>{$row['firstname']}</td>";
                                                echo "<td>{$row['lastname']}</td>";
                                                echo "<td>{$row['username']}</td>";
                                                echo "<td>{$row['email']}</td>";
                                                echo "<td><span class='badge {$roleClass} px-3 py-2' style='position: relative;'>{$roleDisplay}</span></td>";
                                                echo "<td><span class='badge {$statusClass} px-3 py-2' style='position: relative;'>" . ucfirst($status) . "</span></td>";
                                                echo "<td>
                                                        <button class='btn btn-sm btn-primary view-btn' data-id='{$row['id']}'><i class='bi bi-eye'></i></button>
                                                        <button class='btn btn-sm btn-success edit-btn' data-id='{$row['id']}'><i class='bi bi-pencil'></i></button>
                                                        <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'><i class='bi bi-trash'></i></button>
                                                    </td>";
                                                echo "</tr>";
                                                
                                                $serial--;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- </div>
    </div> -->
    <script>
    function addUser() {
        Swal.fire({
            title: 'Add New User',
            html: `
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 position-relative">
                                <input type="text" class="form-control" id="firstname" placeholder="First Name" required>
                                <label for="firstname">First Name</label>
                                <i class="bi bi-person input-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3 position-relative">
                                <input type="text" class="form-control" id="lastname" placeholder="Last Name" required>
                                <label for="lastname">Last Name</label>
                                <i class="bi bi-person input-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating role-select mb-3 position-relative">
                        <select class="form-control" id="role" required>
                            <option value="">Select Role</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                        </select>
                        <label for="role">Role</label>
                        <i class="bi bi-shield-lock input-icon"></i>
                    </div>
                    
                    <div class="role-info">
                        <small>
                            <strong>Role Permissions:</strong><br>
                            • Super Admin: Full system access and control<br>
                            • Admin: Manage users and content<br>
                            • Manager: Basic management tasks
                        </small>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="email" class="form-control" id="email" placeholder="Email" required>
                        <label for="email">Email Address</label>
                        <i class="bi bi-envelope input-icon"></i>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="text" class="form-control" id="username" placeholder="Username" required>
                        <label for="username">Username</label>
                        <i class="bi bi-person-badge input-icon"></i>
                    </div>

                    <div class="form-floating mb-2 position-relative">
                        <input type="password" class="form-control" id="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <i class="bi bi-lock input-icon"></i>
                    </div>
                    
                    <div class="password-requirements">
                        <small>Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.</small>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" required>
                        <label for="confirm_password">Confirm Password</label>
                        <i class="bi bi-lock-fill input-icon"></i>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add User',
            customClass: {
                popup: 'signup-container'
            },
            preConfirm: () => {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (password !== confirmPassword) {
                    Swal.showValidationMessage('Passwords do not match');
                    return false;
                }

                return {
                    firstname: document.getElementById('firstname').value,
                    lastname: document.getElementById('lastname').value,
                    username: document.getElementById('username').value,
                    email: document.getElementById('email').value,
                    password: password,
                    role: document.getElementById('role').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('add_user.php', {
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
                            'Added!',
                            'User has been added successfully.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to add user');
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
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Edit User
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`get_user.php?id=${id}`)
                    .then(response => response.json())
                    .then(user => {
                        Swal.fire({
                            title: 'Edit User',
                            html: `
                                <form id="editUserForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 position-relative">
                                                <input type="text" class="form-control" id="firstname" value="${user.firstname}" placeholder="First Name" required>
                                                <label for="firstname">First Name</label>
                                                <i class="bi bi-person input-icon"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 position-relative">
                                                <input type="text" class="form-control" id="lastname" value="${user.lastname}" placeholder="Last Name" required>
                                                <label for="lastname">Last Name</label>
                                                <i class="bi bi-person input-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-floating role-select mb-3 position-relative">
                                        <select class="form-control" id="role" required>
                                            <option value="super_admin" ${user.role === 'super_admin' ? 'selected' : ''}>Super Admin</option>
                                            <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                            <option value="manager" ${user.role === 'manager' ? 'selected' : ''}>Manager</option>
                                        </select>
                                        <label for="role">Role</label>
                                        <i class="bi bi-shield-lock input-icon"></i>
                                    </div>
                                    
                                    <div class="role-info">
                                        <small>
                                            <strong>Role Permissions:</strong><br>
                                            • Super Admin: Full system access and control<br>
                                            • Admin: Manage users and content<br>
                                            • Manager: Basic management tasks
                                        </small>
                                    </div>

                                    <div class="form-floating mb-3 position-relative">
                                        <input type="email" class="form-control" id="email" value="${user.email}" placeholder="Email" required>
                                        <label for="email">Email Address</label>
                                        <i class="bi bi-envelope input-icon"></i>
                                    </div>

                                    <div class="form-floating mb-3 position-relative">
                                        <input type="text" class="form-control" id="username" value="${user.username}" placeholder="Username" required>
                                        <label for="username">Username</label>
                                        <i class="bi bi-person-badge input-icon"></i>
                                    </div>

                                    <div class="form-floating mb-2 position-relative">
                                        <input type="password" class="form-control" id="password" placeholder="New Password">
                                        <label for="password">New Password (leave blank to keep current)</label>
                                        <i class="bi bi-lock input-icon"></i>
                                    </div>
                                    
                                    <div class="password-requirements">
                                        <small>Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.</small>
                                    </div>

                                    <div class="form-floating mb-3 position-relative">
                                        <select class="form-control" id="status" required>
                                            <option value="active" ${user.status === 'active' ? 'selected' : ''}>Active</option>
                                            <option value="inactive" ${user.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                        <label for="status">Status</label>
                                        <i class="bi bi-toggle-on input-icon"></i>
                                    </div>
                                </form>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Update User',
                            customClass: {
                                popup: 'signup-container'
                            },
                            preConfirm: () => {
                                const password = document.getElementById('password').value;

                                return {
                                    id: id,
                                    firstname: document.getElementById('firstname').value,
                                    lastname: document.getElementById('lastname').value,
                                    username: document.getElementById('username').value,
                                    email: document.getElementById('email').value,
                                    password: password, // Will be empty if not changed
                                    role: document.getElementById('role').value,
                                    status: document.getElementById('status').value
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch('update_user.php', {
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
                                            'Updated!',
                                            'User has been updated successfully.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        throw new Error(data.message || 'Failed to update user');
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

        // Delete User
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Delete User?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('delete_user.php', {
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
                                    'User has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                throw new Error(data.message || 'Failed to delete user');
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

        // View User
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`get_user.php?id=${id}`)
                    .then(response => response.json())
                    .then(user => {
                        Swal.fire({
                            title: 'User Details',
                            html: `
                                <div class="text-start">
                                    <div class="mb-3">
                                        <strong>Name:</strong> ${user.firstname} ${user.lastname}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Username:</strong> ${user.username}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Email:</strong> ${user.email}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Role:</strong> <span class="badge ${user.role === 'super_admin' ? 'bg-danger' : user.role === 'admin' ? 'bg-success' : 'bg-primary'}">${user.role.replace('_', ' ').toUpperCase()}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Status:</strong> <span class="badge ${user.status === 'active' ? 'bg-success' : 'bg-danger'}">${user.status.toUpperCase()}</span>
                                    </div>
                                </div>
                            `,
                            confirmButtonText: 'Close'
                        });
                    });
            });
        });
    });
    </script>

</body>
</html> 
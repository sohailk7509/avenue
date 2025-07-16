<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup - Avenza Avenue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .signup-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transform: translateY(0);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .signup-container:hover {
            transform: translateY(-5px);
        }
        .signup-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1e3c72, #2a5298, #1e3c72);
            background-size: 200% 100%;
            animation: gradientMove 3s linear infinite;
        }
        @keyframes gradientMove {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
        .signup-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .signup-logo h1 {
            color: #1e3c72;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .signup-logo p {
            color: #666;
            font-size: 1rem;
            opacity: 0.8;
        }
        .form-floating {
            margin-bottom: 1.25rem;
        }
        .form-floating > .form-control {
            padding: 1.2rem 0.75rem;
            border: 2px solid #e1e5ee;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .form-floating > .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 4px rgba(30,60,114,0.1);
        }
        .form-floating > label {
            padding: 1.2rem 0.75rem;
            color: #666;
        }
        .btn-primary {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #2a5298, #1e3c72);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30,60,114,0.2);
        }
        .btn-primary:hover::after {
            left: 100%;
        }
        .signup-footer {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
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
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-link a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .login-link a:hover {
            color: #2a5298;
            text-decoration: underline;
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

        .role-badge.super-admin {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .role-badge.admin {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .role-badge.manager {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <div class="signup-logo">
                <h1>Avenza Avenue</h1>
                <p>Create Admin Account</p>
            </div>
            <form action="register.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3 position-relative">
                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>
                            <label for="firstname">First Name</label>
                            <i class="bi bi-person input-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 position-relative">
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" required>
                            <label for="lastname">Last Name</label>
                            <i class="bi bi-person input-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating role-select mb-3 position-relative">
                    <select class="form-control" id="role" name="role" required>
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
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    <label for="email">Email Address</label>
                    <i class="bi bi-envelope input-icon"></i>
                </div>

                <div class="form-floating mb-3 position-relative">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username">Username</label>
                    <i class="bi bi-person-badge input-icon"></i>
                </div>

                <div class="form-floating mb-2 position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <i class="bi bi-lock input-icon"></i>
                </div>
                
                <div class="password-requirements">
                    <small>Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.</small>
                </div>

                <div class="form-floating mb-4 position-relative">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    <label for="confirm_password">Confirm Password</label>
                    <i class="bi bi-lock-fill input-icon"></i>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>

                <div class="login-link">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </form>
        </div>
        <div class="signup-footer">
            © 2024 Avenza Avenue. All rights reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*]/.test(password)
            };
            
            // You can add visual feedback here if needed
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Add role badge display
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const existingBadge = document.querySelector('.role-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            if (role) {
                const badge = document.createElement('span');
                badge.className = `role-badge ${role.replace('_', '-')}`;
                badge.textContent = role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                this.parentElement.appendChild(badge);
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and redirect to login page
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'signup.php'; // Redirect to login page
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.'
                });
            });
        });
    </script>
</body>
</html> 
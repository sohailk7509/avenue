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
    <title>Add New Blog - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            display: none;
        }
        .note-editor {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="row content left-menu-space main-content">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Blog</h3>
                </div>
                <div class="card-body">
                    <form id="blogForm" action="save_blog.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Blog Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <img id="imagePreview" class="preview-image mt-2" src="#" alt="Preview">
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="draft" value="draft" checked>
                                <label class="form-check-label" for="draft">Draft</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="published" value="published">
                                <label class="form-check-label" for="published">Publish</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="location.href='blogs.php'">
                                <i class="bi bi-arrow-left"></i> Back to Blogs
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Blog
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Initialize Summernote
        $('#content').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Image Preview
        $("#image").change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#imagePreview')
                        .attr('src', event.target.result)
                        .css('display', 'block');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form Submit
        $('#blogForm').submit(function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'save_blog.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Blog saved successfully!'
                            }).then(() => {
                                window.location.href = 'blogs.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'An error occurred'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Invalid server response'
                        });
                        console.error('Server response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save blog. Please try again.'
                    });
                    console.error('Ajax error:', status, error);
                }
            });
        });
    });
    </script>
</body>
</html> 
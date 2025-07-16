<style>
  /* Topbar styling */
  .topbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 999;
    padding: 0 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
  }
  
  .topbar.with-sidebar {
    left: 234px;
    width: calc(100% - 234px);
  }
  
  .topbar-brand {
    display: flex;
    align-items: center;
    font-weight: 600;
  }
  
  .topbar-brand img {
    height: 40px;
    margin-right: 10px;
  }
  
  .topbar-right {
    display: flex;
    align-items: center;
  }
  
  .topbar-right .dropdown {
    margin-left: 20px;
  }
  
  .topbar-right .dropdown-toggle {
    cursor: pointer;
    font-size: 18px;
  }
  
  .topbar-right .dropdown-menu {
    margin-top: 10px;
  }
  
  .notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    border-radius: 50%;
    background: #ff5c5c;
    color: white;
    width: 18px;
    height: 18px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .profile-img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
  }
  
  /* Main content area */
  .main-content {
    margin-left: 234px !important; /* Match the sidebar width */
    padding: 80px 20px 20px;
    min-height: 100vh;
    transition: all 0.3s ease;
  }
  
  /* Sidebar styling */
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 234px;
    background: #343a40;
    color: white;
    overflow-y: auto;
    z-index: 1000;
    transition: all 0.3s ease;
  }
  
  .logo-details {
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .sidebar-logo {
    width: 30px;
    height: 30px;
    margin-right: 10px;
  }
  
  .logo_name {
    font-size: 18px;
    font-weight: 600;
  }
  
  .profile-section {
    padding: 15px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .profile-info {
    margin-left: 10px;
  }
  
  .admin-name {
    font-size: 14px;
    font-weight: 500;
    display: block;
  }
  
  .admin-role {
    font-size: 12px;
    opacity: 0.7;
  }
  
  .nav-links {
    padding: 0;
    margin: 0;
    list-style: none;
  }
  
  .nav-links li {
    position: relative;
  }
  
  .nav-links li a {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
  }
  
  .nav-links li a:hover,
  .nav-links li.active a {
    background: rgba(255, 255, 255, 0.1);
  }
  
  .nav-links li a i {
    min-width: 30px;
    font-size: 18px;
  }
  
  .badge {
    position: absolute;
    right: 15px;
    background: #ff5c5c;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
  }
  
  /* Submenu styling */
  .has-submenu > a {
    position: relative;
  }
  
  .toggle-down {
    position: absolute;
    right: 15px;
    transition: transform 0.3s ease;
  }
  
  .submenu {
    list-style: none;
    padding-left: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  
  .submenu.show {
    max-height: 1000px;
  }
  
  .submenu li a {
    padding-left: 35px;
    background: rgba(0, 0, 0, 0.1);
  }
  
  /* Mobile responsiveness */
  @media (max-width: 768px) {
    .sidebar {
      left: -234px; /* Hide the sidebar by default on mobile */
    }
    
    .sidebar.active {
      left: 0; /* Show sidebar when active */
    }
    
    .topbar.with-sidebar {
      left: 0;
      width: 100%;
    }
    
    .topbar.with-sidebar.shifted {
      left: 234px;
    }
    
    .main-content {
      margin-left: 0 !important;
    }
    
    .main-content.shifted {
      margin-left: 234px !important; /* Show sidebar pushes content */
    }
  }
  
  /* Toggle button */
  .sidebar-toggle-btn {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid #dee2e6;
    color: #343a40;
    position: relative;
    z-index: 1001;
  }
  
  /* Overlay when sidebar is active */
  .sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
  }
  
  .sidebar-overlay.active {
    display: block;
  }
  
  /* Mobile-only toggle button */
  @media (min-width: 769px) {
    .sidebar-toggle-btn {
      display: none;
    }
  }
  
  .sidebar-toggle-btn {
    display: block;
    background: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #333;
  }
  
  .sidebar-toggle-btn i {
    transition: all 0.3s ease;
  }
</style>

<div class="topbar with-sidebar">
  <div class="topbar-brand">
    <button class="sidebar-toggle-btn">
      <i class="bi bi-list"></i>
    </button>
    <a href="index.php" class="d-none d-lg-flex align-items-center text-decoration-none">
      <img src="../assets/img/favicon.png" alt="Logo">
      <span>Admin Portal</span>
    </a>
  </div>
  
  <div class="topbar-right">
    <div class="dropdown">
      <span class="position-relative dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-5"></i>
        <span class="badge bg-danger rounded-pill" style="font-size: 8px; position: absolute; top: 0; right: 0;">3</span>
      </span>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">New booking request</a></li>
        <li><a class="dropdown-item" href="#">New inquiry received</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">View all notifications</a></li>
      </ul>
    </div>
    
    <div class="dropdown">
      <span class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
        <img src="../assets/img/profile-default1.jpg" alt="Profile" class="profile-img">
        <span class="d-none d-md-inline"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></span>
      </span>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="sidebar">
    <div class="logo-details">
        <img src="../assets/img/favicon.png" alt="Logo" class="sidebar-logo">
        <span class="logo_name">Avenza Avenue</span>
        <i class="bi bi-x-lg mobile-nav-toggle d-md-none"></i>
    </div>

    <div class="profile-section">
        <img src="../assets/img/profile-default1.jpg" alt="Profile" class="profile-img">
        <div class="profile-info">
            <span class="admin-name"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></span>
            <span class="admin-role"><?php echo isset($_SESSION['admin_role']) ? ucwords(str_replace('_', ' ', $_SESSION['admin_role'])) : 'Administrator'; ?></span>
        </div>
    </div>

    <ul class="nav-links">
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
            <a href="index.php">
                <i class="bi bi-grid-fill"></i>
                <span class="link_name">Dashboard</span>
            </a>
        </li>
        
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'bookings.php') ? 'active' : ''; ?>">
            <a href="bookings.php">
                <i class="bi bi-calendar-check-fill"></i>
                <span class="link_name">Bookings</span>
            </a>
        </li>

        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'messages.php') ? 'active' : ''; ?>">
            <a href="messages.php">
                <i class="bi bi-envelope-fill"></i>
                <span class="link_name">Messages</span>
            </a>
        </li>

        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'user.php') ? 'active' : ''; ?>">
            <a href="user.php">
                <i class="bi bi-people-fill"></i>
                <span class="link_name">Users</span>
            </a>
        </li>
        
        <li class="nav-item submenu-parent <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['blogs.php', 'add_blog.php'])) ? 'active' : ''; ?>">
            <a href="#" class="submenu-toggle">
                <i class="bi bi-file-text-fill"></i>
                <span class="link_name">Blog Management</span>
                <i class="bi bi-chevron-down toggle-down"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="add_blog.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'add_blog.php') ? 'active' : ''; ?>">
                        <i class="bi bi-plus-lg"></i>
                        <span>Add New Blog</span>
                    </a>
                </li>
                <li>
                    <a href="blogs.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'blogs.php') ? 'active' : ''; ?>">
                        <i class="bi bi-journal-text"></i>
                        <span>Blogs Record</span>
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span class="link_name">Logout</span>
            </a>
        </li>
    </ul>
</div>

<div class="sidebar-overlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Elements
  const sidebar = document.querySelector('.sidebar');
  const toggleBtn = document.querySelector('.sidebar-toggle-btn');
  const overlay = document.querySelector('.sidebar-overlay');
  const mainContent = document.querySelector('.main-content');
  const topbar = document.querySelector('.topbar.with-sidebar');
  
  // Toggle sidebar function (single implementation)
  function toggleSidebar() {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    if (mainContent) {
      mainContent.classList.toggle('shifted');
    }
    
    if (topbar) {
      topbar.classList.toggle('shifted');
    }
  }
  
  // Add click event to toggle button
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      toggleSidebar();
    });
  }
  
  // Close sidebar when clicking on overlay
  if (overlay) {
    overlay.addEventListener('click', toggleSidebar);
  }
  
  // Toggle submenu
  const submenuToggles = document.querySelectorAll('.submenu-toggle');
  submenuToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Get the parent li element
      const parent = this.closest('.submenu-parent');
      // Get the submenu
      const submenu = this.nextElementSibling;
      
      // Toggle submenu visibility
      submenu.classList.toggle('show');
      
      // Toggle chevron icon
      const chevron = this.querySelector('.toggle-down');
      if (chevron) {
        chevron.style.transform = submenu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0)';
      }
    });
  });
  
  // Auto-open submenu if a child is active
  const activeSubmenuItems = document.querySelectorAll('.submenu .active');
  activeSubmenuItems.forEach(item => {
    const parentSubmenu = item.closest('.submenu');
    if (parentSubmenu) {
      parentSubmenu.classList.add('show');
      
      const parentToggle = parentSubmenu.previousElementSibling;
      if (parentToggle && parentToggle.querySelector('.toggle-down')) {
        parentToggle.querySelector('.toggle-down').style.transform = 'rotate(180deg)';
      }
    }
  });
  
  // Initialize Bootstrap components if Bootstrap 5 is loaded
  if (typeof bootstrap !== 'undefined') {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });
  }
});
</script> 

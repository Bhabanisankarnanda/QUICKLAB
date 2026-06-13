<div class="col-md-3 sidebar">
    <div class="p-3">
        <h4>QUICKLAB Admin</h4>
        <p class="text-muted">Welcome, <?php echo $_SESSION['admin_name']; ?></p>
    </div>
    <nav>
        <a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
        <a href="manage_tests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_tests.php' ? 'active' : ''; ?>">
            <i class="fas fa-vial me-2"></i>Manage Tests
        </a>
        <a href="manage_packages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_packages.php' ? 'active' : ''; ?>">
            <i class="fas fa-box me-2"></i>Manage Packages
        </a>
        <a href="view_bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_bookings.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check me-2"></i>View Bookings
        </a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </nav>
</div>
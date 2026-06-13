<?php
require_once "dbcon.php"; // Make sure this is included
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Rest of your navbar code -->

<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="index.php">
            <img src="./assets/logo.jpeg" width="55" class="me-2">
            QUICKLAB - DIAGNOSTICS
        </a>

        <!-- Mobile Menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navMenu">

            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link fw-semibold" href="homepage.php">Home</a></li>
                
                <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                    <!-- Show when user is logged in -->
                    <li class="nav-item dropdown">
                        <a class="nav-link fw-semibold dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>Hello, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="my_reports.php"><i class="fas fa-file-medical me-2"></i>My Reports</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Show when user is not logged in -->
                    <li class="nav-item"><a class="nav-link fw-semibold" href="register.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="login.php">Log In</a></li>
                <?php endif; ?>

                <li class="nav-item"><a class="nav-link fw-semibold" href="./allpackages.php">Health Packages</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="./alltests.php">Book a Test</a></li>
                <li class="nav-item">
    <a class="nav-link fw-semibold" href="cart.php">
        Cart 
        <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
            <span class="badge bg-primary"><?php echo $_SESSION['cart_count']; ?></span>
        <?php endif; ?>
    </a>
</li>

                <!-- ADMIN ONLY -->
                
                    <li class="nav-item"><a class="nav-link fw-bold text-danger" href="admin_dashboard.php">Admin Panel</a></li>
            
            </ul>

            <!-- Right Button -->
            <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                <a class="btn btn-primary" href="download_report.php">
                    <i class="fas fa-download me-2"></i>My Bookings
                </a>
            <?php else: ?>
                <a class="btn btn-outline-primary" href="login.php">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Download <br> Reports
                </a>
            <?php endif; ?>

        </div>
    </div>
</nav>
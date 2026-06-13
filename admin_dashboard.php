<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}


$bookings_count = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$tests_count = $conn->query("SELECT COUNT(*) as count FROM tests")->fetch_assoc()['count'];
$packages_count = $conn->query("SELECT COUNT(*) as count FROM packages")->fetch_assoc()['count'];
$bookings_count = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #2c3e50;
            min-height: 100vh;
            color: white;
            padding: 0;
        }
        .sidebar-header {
            background: #1a252f;
            padding: 20px;
            border-bottom: 1px solid #34495e;
        }
        .sidebar-nav {
            padding: 0;
        }
        .sidebar-nav a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            border-bottom: 1px solid #34495e;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: #34495e;
            color: #3498db;
            border-left: 4px solid #3498db;
        }
        .main-content {
            padding: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .stats-card p {
            color: #7f8c8d;
            margin: 0;
        }
        .stats-card i {
            font-size: 3rem;
            opacity: 0.7;
            margin-bottom: 15px;
        }
        .card-primary { border-left-color: #3498db; }
        .card-success { border-left-color: #27ae60; }
        .card-warning { border-left-color: #f39c12; }
        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px -30px;
            border-bottom: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4 class="mb-0">QUICKLAB Admin</h4>
                    <small class="text-muted">Welcome, <?php echo $_SESSION['admin_name']; ?></small>
                </div>
                <nav class="sidebar-nav">
                    <a href="admin_dashboard.php" class="active">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="manage_tests.php">
                        <i class="fas fa-vial me-2"></i>Manage Tests
                    </a>
                    <a href="manage_packages.php">
                        <i class="fas fa-box me-2"></i>Manage Packages
                    </a>
                    <a href="view_bookings.php">
                        <i class="fas fa-calendar-check me-2"></i>View Bookings
                    </a>
                    <a href="admin_logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="page-header">
                    <h2 class="mb-0">Admin Dashboard</h2>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="stats-card card-primary">
                            <i class="fas fa-vial text-primary"></i>
                            <h3><?php echo $tests_count; ?></h3>
                            <p>Total Tests</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card card-success">
                            <i class="fas fa-box text-success"></i>
                            <h3><?php echo $packages_count; ?></h3>
                            <p>Total Packages</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card card-warning">
                            <i class="fas fa-calendar-check text-warning"></i>
                            <h3><?php echo $bookings_count; ?></h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
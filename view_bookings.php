<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$bookings = $conn->query("
    SELECT b.*, u.name as user_name, u.phone, u.email,
           COALESCE(t.test_name, p.package_name) as item_name,
           b.type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN tests t ON b.test_id = t.id AND b.type = 'test'
    LEFT JOIN packages p ON b.package_id = p.id AND b.type = 'package'
    ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - QUICKLAB</title>
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
        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px -30px;
            border-bottom: 1px solid #e9ecef;
        }
        .table-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #2c3e50;
        }
        .badge-test { background: #3498db; }
        .badge-package { background: #27ae60; }
        .badge-pending { background: #f39c12; }
        .badge-confirmed { background: #27ae60; }
        .badge-completed { background: #2980b9; }
        .badge-cancelled { background: #e74c3c; }
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
                    <a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="manage_tests.php">
                        <i class="fas fa-vial me-2"></i>Manage Tests
                    </a>
                    <a href="manage_packages.php">
                        <i class="fas fa-box me-2"></i>Manage Packages
                    </a>
                    <a href="view_bookings.php" class="active">
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
                    <h2 class="mb-0">View Bookings</h2>
                </div>

                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User Details</th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Payment Status</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($booking = $bookings->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $booking['id']; ?></strong></td>
                                    <td>
                                        <div class="fw-bold"><?php echo $booking['user_name']; ?></div>
                                        <small class="text-muted"><?php echo $booking['phone']; ?></small><br>
                                        <small class="text-muted"><?php echo $booking['email']; ?></small>
                                    </td>
                                    <td><?php echo $booking['item_name']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $booking['type'] == 'test' ? 'badge-test' : 'badge-package'; ?>">
                                            <?php echo ucfirst($booking['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo $booking['booking_date']; ?></div>
                                        <small class="text-muted"><?php echo $booking['time_slot']; ?></small>
                                    </td>
                                    <td>₹<?php echo $booking['price']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo substr($booking['address'], 0, 50); ?>...</small>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
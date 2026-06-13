<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle test deletion
if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM tests WHERE id = $delete_id");
    $_SESSION['message'] = "Test deleted successfully!";
    header("Location: manage_tests.php");
    exit();
}

$tests = $conn->query("SELECT * FROM tests ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tests - QUICKLAB</title>
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
        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
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
                    <a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="manage_tests.php" class="active">
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Manage Tests</h2>
                        <a href="add_test.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Test
                        </a>
                    </div>
                </div>

                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Test Name</th>
                                    <th>Original Price</th>
                                    <th>Discounted Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($test = $tests->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $test['id']; ?></strong></td>
                                    <td><?php echo $test['test_name']; ?></td>
                                    <td>₹<?php echo $test['original_price']; ?></td>
                                    <td>₹<?php echo $test['discounted_price']; ?></td>
                                    <td>
                                        <a href="edit_test.php?id=<?php echo $test['id']; ?>" class="btn btn-warning btn-sm btn-action">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="manage_tests.php?delete_id=<?php echo $test['id']; ?>" 
                                           class="btn btn-danger btn-sm btn-action" 
                                           onclick="return confirm('Are you sure you want to delete this test?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
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
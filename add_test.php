<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$test_id = $_GET['id'];
$test = $conn->query("SELECT * FROM tests WHERE id = $test_id")->fetch_assoc();

if($_POST) {
    $test_name = $_POST['test_name'];
    $description = $_POST['description'];
    $original_price = $_POST['original_price'];
    $discounted_price = $_POST['discounted_price'];
    
    // Handle image update
    $image = $test['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/testimages/" . $image);
    }
    
    $stmt = $conn->prepare("UPDATE tests SET test_name=?, description=?, original_price=?, discounted_price=?, image=? WHERE id=?");
    $stmt->bind_param("ssddsi", $test_name, $description, $original_price, $discounted_price, $image, $test_id);
    
    if($stmt->execute()) {
        $_SESSION['message'] = "Test updated successfully!";
        header("Location: manage_tests.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Test - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-header-bg: #1a252f;
            --sidebar-active: #34495e;
            --sidebar-border: #34495e;
            --primary-color: #3498db;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            background: var(--sidebar-bg);
            min-height: 100vh;
            color: white;
            padding: 0;
        }
        
        .sidebar-header {
            background: var(--sidebar-header-bg);
            padding: 20px;
            border-bottom: 1px solid var(--sidebar-border);
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .sidebar-header .text-muted {
            color: #bdc3c7 !important;
            font-size: 0.875rem;
        }
        
        .sidebar-nav {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        
        .sidebar-nav a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            border-bottom: 1px solid var(--sidebar-border);
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .sidebar-nav a:hover {
            background: var(--sidebar-active);
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
        }
        
        .sidebar-nav a.active {
            background: var(--sidebar-active);
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
            font-weight: 500;
        }
        
        .main-content {
            padding: 0;
        }
        
        .page-header {
            background: white;
            padding: 25px 30px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin: 0 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .btn-secondary {
            background: #95a5a6;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="sidebar-header">
                    <h4>QUICKLAB Admin</h4>
                    <small class="text-muted">Welcome, <?php echo $_SESSION['admin_name']; ?></small>
                </div>
                <nav class="sidebar-nav">
                    <a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="manage_tests.php" class="active">
                        <i class="fas fa-vial"></i>Manage Tests
                    </a>
                    <a href="manage_packages.php">
                        <i class="fas fa-box"></i>Manage Packages
                    </a>
                    <a href="view_bookings.php">
                        <i class="fas fa-calendar-check"></i>View Bookings
                    </a>
                    <a href="admin_logout.php">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-0">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Edit Test</h2>
                        <a href="manage_tests.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tests
                        </a>
                    </div>
                </div>

                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Test Name</label>
                                    <input type="text" name="test_name" class="form-control" value="<?php echo $test['test_name']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Test Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Current: <?php echo $test['image']; ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $test['description']; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Original Price (₹)</label>
                                    <input type="number" name="original_price" class="form-control" value="<?php echo $test['original_price']; ?>" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Discounted Price (₹)</label>
                                    <input type="number" name="discounted_price" class="form-control" value="<?php echo $test['discounted_price']; ?>" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Test
                            </button>
                            <a href="manage_tests.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
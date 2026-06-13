<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if($_POST) {
    $package_name = $_POST['package_name'];
    $description = $_POST['description'];
    $original_price = $_POST['original_price'];
    $discounted_price = $_POST['discounted_price'];
    
    // Handle image upload
    $image = 'default_package.jpg';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/packageimages/" . $image);
    }
    
    $stmt = $conn->prepare("INSERT INTO packages (package_name, description, original_price, discounted_price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $package_name, $description, $original_price, $discounted_price, $image);
    
    if($stmt->execute()) {
        $_SESSION['message'] = "Package added successfully!";
        header("Location: manage_packages.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Same sidebar styles as manage_packages.php */
        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { background: #2c3e50; min-height: 100vh; color: white; padding: 0; }
        .sidebar-header { background: #1a252f; padding: 20px; border-bottom: 1px solid #34495e; }
        .sidebar-nav { padding: 0; }
        .sidebar-nav a { color: #ecf0f1; text-decoration: none; padding: 15px 20px; display: block; border-bottom: 1px solid #34495e; transition: all 0.3s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #34495e; color: #3498db; border-left: 4px solid #3498db; }
        .main-content { padding: 30px; }
        .page-header { background: white; padding: 20px 30px; margin: -30px -30px 30px -30px; border-bottom: 1px solid #e9ecef; }
        .form-card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
                    <a href="manage_packages.php" class="active">
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
                    <h2 class="mb-0">Add New Package</h2>
                </div>
                
                <div class="form-card">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Package Name</label>
                                    <input type="text" name="package_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Package Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the package and what it includes"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Original Price (₹)</label>
                                    <input type="number" name="original_price" class="form-control" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discounted Price (₹)</label>
                                    <input type="number" name="discounted_price" class="form-control" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add Package</button>
                        <a href="manage_packages.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
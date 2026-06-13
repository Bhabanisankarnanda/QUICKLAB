<?php
session_start();
require_once "dbcon.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// First, check what columns exist in the bookings table
$check_columns = "SHOW COLUMNS FROM bookings";
$columns_result = $conn->query($check_columns);
$existing_columns = [];
while ($column = $columns_result->fetch_assoc()) {
    $existing_columns[] = $column['Field'];
}

// Build query based on available columns
$has_report_file = in_array('report_file', $existing_columns);
$has_report_date = in_array('report_date', $existing_columns);
$has_booking_number = in_array('booking_number', $existing_columns);
$has_type = in_array('type', $existing_columns);

// Build the query dynamically based on available columns
$select_fields = "b.*";
$join_condition = "";

if ($has_type) {
    $select_fields .= ", COALESCE(t.test_name, p.package_name) as item_name";
    $join_condition = "LEFT JOIN tests t ON b.item_id = t.id AND b.type = 'test'
                       LEFT JOIN packages p ON b.item_id = p.id AND b.type = 'package'";
} else {
    // Fallback - assume all are tests
    $select_fields .= ", t.test_name as item_name";
    $join_condition = "LEFT JOIN tests t ON b.test_id = t.id";
}

$qry = "SELECT $select_fields
        FROM bookings b
        $join_condition
        WHERE b.user_id = ? AND b.status = 'completed'";

// Only filter by report_file if the column exists
if ($has_report_file) {
    $qry .= " AND b.report_file IS NOT NULL";
}

$qry .= " ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle report download
if (isset($_GET['download']) && isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    // Verify the booking belongs to the logged-in user and is completed
    $verify_qry = "SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'completed'";
    $verify_stmt = $conn->prepare($verify_qry);
    $verify_stmt->bind_param("ii", $booking_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $booking = $verify_result->fetch_assoc();
        
        // Check if report_file exists and file exists
        if ($has_report_file && !empty($booking['report_file'])) {
            $file_path = "reports/" . $booking['report_file'];
            
            if (file_exists($file_path)) {
                // Set headers for download
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="report_' . $booking_id . '.pdf"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));
                readfile($file_path);
                exit;
            } else {
                $_SESSION['error'] = "Report file not found on server.";
            }
        } else {
            $_SESSION['error'] = "Report is not available for download yet.";
        }
    } else {
        $_SESSION['error'] = "Invalid report request.";
    }
    
    header("Location: my_reports.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reports-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .report-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .report-card:hover {
            transform: translateY(-5px);
        }
        
        .report-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .report-badge {
            background: #e7f3ff;
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .file-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <!-- Header -->
    <div class="reports-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">My Lab Reports</h1>
                    <p class="lead mb-0">Access and download your diagnostic test reports</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-file-medical-alt fa-4x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($report = $result->fetch_assoc()): 
                    // Generate booking number
                    $booking_number = $has_booking_number && !empty($report['booking_number']) 
                        ? $report['booking_number'] 
                        : 'QL' . str_pad($report['id'], 6, '0', STR_PAD_LEFT);
                    
                    // Check if report is available
                    $report_available = $has_report_file && !empty($report['report_file']);
                    $file_path = $report_available ? "reports/" . $report['report_file'] : '';
                    $file_exists = $report_available && file_exists($file_path);
                    
                    // Get report date
                    $report_date = $has_report_date && !empty($report['report_date']) 
                        ? $report['report_date'] 
                        : $report['booking_date'];
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card report-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="report-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            
                            <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($report['item_name'] ?? 'Test Report'); ?></h5>
                            
                            <div class="report-badge mb-3">
                                <i class="fas fa-hashtag me-1"></i><?php echo $booking_number; ?>
                            </div>
                            
                            <div class="file-info">
                                <div class="row text-start small">
                                    <div class="col-12 mb-2">
                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                        <strong>Completed:</strong> 
                                        <?php echo date('d M Y', strtotime($report_date)); ?>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <i class="fas fa-file me-2 text-muted"></i>
                                        <strong>Format:</strong> PDF
                                    </div>
                                    <div class="col-12">
                                        <i class="fas fa-database me-2 text-muted"></i>
                                        <strong>Status:</strong> 
                                        <?php if ($file_exists): ?>
                                            <span class="text-success">Available</span>
                                        <?php elseif ($report_available): ?>
                                            <span class="text-warning">Processing</span>
                                        <?php else: ?>
                                            <span class="text-info">Completed</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <?php if ($file_exists): ?>
                                    <a href="my_reports.php?download=true&id=<?php echo $report['id']; ?>" 
                                       class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-download me-2"></i>Download Report
                                    </a>
                                    <a href="reports/<?php echo $report['report_file']; ?>" 
                                       class="btn btn-outline-primary w-100" 
                                       target="_blank">
                                        <i class="fas fa-eye me-2"></i>View Online
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        <i class="fas fa-clock me-2"></i>
                                        <?php echo $report_available ? 'Report Processing' : 'Report Not Available'; ?>
                                    </button>
                                    <?php if (!$report_available): ?>
                                        <small class="text-muted mt-2 d-block">
                                            Contact support for your report
                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="card report-card">
                        <div class="card-body empty-state">
                            <i class="fas fa-file-medical"></i>
                            <h3 class="text-muted">No Completed Tests</h3>
                            <p class="text-muted mb-4">You don't have any completed tests yet. Your test reports will appear here once they are completed and processed.</p>
                            
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body text-center p-4">
                                                    <i class="fas fa-vial fa-2x text-primary mb-3"></i>
                                                    <h5>Book a Test</h5>
                                                    <p class="text-muted small">Schedule your diagnostic tests</p>
                                                    <a href="alltests.php" class="btn btn-outline-primary btn-sm">Browse Tests</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body text-center p-4">
                                                    <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                                                    <h5>Check Bookings</h5>
                                                    <p class="text-muted small">View your test bookings status</p>
                                                    <a href="download_report.php" class="btn btn-outline-primary btn-sm">View Bookings</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="text-white pt-5 pb-3 mt-5" style="background: #003049;">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-flask fa-2x text-primary me-2"></i>
                        <h4 class="fw-bold mb-0">QuickLab Diagnostics</h4>
                    </div>
                    <p class="text-light opacity-75">Your trusted partner for accurate diagnostic testing. We provide reliable lab results with free home collection services.</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-light opacity-75 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="alltests.php" class="text-light opacity-75 text-decoration-none">Lab Tests</a></li>
                        <li class="mb-2"><a href="allpackages.php" class="text-light opacity-75 text-decoration-none">Packages</a></li>
                        <li class="mb-2"><a href="about.php" class="text-light opacity-75 text-decoration-none">About Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Our Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none"><i class="fas fa-vial me-2"></i>Blood Tests</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none"><i class="fas fa-dna me-2"></i>Pathology Tests</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none"><i class="fas fa-home me-2"></i>Home Collection</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none"><i class="fas fa-building me-2"></i>Corporate Health</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-phone me-2 text-primary"></i> +91 98765 43210</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> info@quicklab.com</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Medical Complex, City</li>
                        <li class="mb-2"><i class="fas fa-clock me-2 text-primary"></i> 24/7 Emergency</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-light opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">&copy; 2025 QuickLab Diagnostics. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light opacity-75 text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-light opacity-75 text-decoration-none me-3">Terms of Service</a>
                    <a href="#" class="text-light opacity-75 text-decoration-none">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
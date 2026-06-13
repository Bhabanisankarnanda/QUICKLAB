<?php
session_start();
require_once "dbcon.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// First, let's try a simple query to see what columns exist
$test_qry = "SHOW COLUMNS FROM bookings";
$test_result = $conn->query($test_qry);
$columns = [];
while($row = $test_result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

// Based on available columns, build the appropriate query
if (in_array('test_id', $columns)) {
    // If using test_id column
    $qry = "SELECT b.*, t.test_name as item_name, t.discounted_price as price
            FROM bookings b
            LEFT JOIN tests t ON b.test_id = t.id
            WHERE b.user_id = ?
            ORDER BY b.booking_date DESC";
} else {
    // Fallback - just get basic booking info
    $qry = "SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC";
}

$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - QUICKLAB</title>
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
        
        .booking-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .booking-card:hover {
            transform: translateY(-3px);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background: #cce7ff;
            color: #004085;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
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
        
        .booking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <!-- Header -->
    <div class="booking-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold">My Bookings</h1>
                    <p class="lead mb-0">View your test reports and booking history</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-file-medical fa-3x opacity-75"></i>
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
                <div class="col-12">
                    <div class="card booking-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Test/Package</th>
                                            <th>Booking Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($booking = $result->fetch_assoc()): 
                                            $status_class = '';
                                            switch($booking['status']) {
                                                case 'completed':
                                                    $status_class = 'status-completed';
                                                    break;
                                                case 'pending':
                                                    $status_class = 'status-pending';
                                                    break;
                                                case 'processing':
                                                    $status_class = 'status-processing';
                                                    break;
                                                case 'cancelled':
                                                    $status_class = 'status-cancelled';
                                                    break;
                                                default:
                                                    $status_class = 'status-pending';
                                            }
                                            
                                            // Generate booking number
                                            $booking_number = isset($booking['booking_number']) ? $booking['booking_number'] : 'QL' . str_pad($booking['id'], 6, '0', STR_PAD_LEFT);
                                            
                                            // Get item name
                                            $item_name = isset($booking['item_name']) ? $booking['item_name'] : 'Test Booking';
                                            
                                            // Check if this might be a package
                                            $item_type = 'test';
                                            if (isset($booking['package_id']) || stripos($item_name, 'package') !== false) {
                                                $item_type = 'package';
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $booking_number; ?></strong>
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item_name); ?></h6>
                                                <small class="text-muted text-uppercase"><?php echo $item_type; ?></small>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y, h:i A', strtotime($booking['booking_date'])); ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <?php if ($booking['status'] == 'completed'): ?>
                                                        <button class="btn btn-primary btn-sm" onclick="downloadReport(<?php echo $booking['id']; ?>)">
                                                            <i class="fas fa-download me-1"></i>Download Report
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                                            <i class="fas fa-clock me-1"></i>Report Not Ready
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
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
        <?php else: ?>
            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="card booking-card">
                        <div class="card-body empty-state">
                            <i class="fas fa-file-medical"></i>
                            <h3 class="text-muted">No Bookings Yet</h3>
                            <p class="text-muted mb-4">You haven't made any bookings yet. Start by exploring our tests and health packages.</p>
                            <a href="alltests.php" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-vial me-2"></i>Browse Tests
                            </a>
                            <a href="allpackages.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-box me-2"></i>View Packages
                            </a>
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

    <script>
        function downloadReport(bookingId) {
            // For now, show an alert. You can implement actual download later
            alert('Report download functionality will be implemented soon for booking #' + bookingId);
            // In future, you can redirect to: window.location.href = 'download_report_file.php?id=' + bookingId;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
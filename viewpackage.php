<?php
session_start();
require_once "dbcon.php";
include_once "navbar.php";
// Check if package ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: allpackages.php");
    exit();
}

$package_id = $_GET['id'];

// Fetch package details from database
$qry = "SELECT * FROM packages WHERE id = ?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    header("Location: allpackages.php");
    exit();
}

$package = $result->fetch_assoc();

// Calculate discount percentage
$off = 0;
if($package['original_price'] > 0) {
    $off = round((($package['original_price'] - $package['discounted_price']) / $package['original_price']) * 100);
}

// Fetch related packages (excluding current package)
$related_qry = "SELECT id, package_name, original_price, discounted_price, image FROM packages WHERE id != ? LIMIT 4";
$related_stmt = $conn->prepare($related_qry);
$related_stmt->bind_param("i", $package_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($package['package_name']); ?> - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .package-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .package-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .package-card:hover {
            transform: translateY(-5px);
        }
        
        .price-tag {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        
        .original-price {
            color: #f8f9fa;
            text-decoration: line-through;
        }
        
        .discount-badge {
            background: #28a745;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list i {
            color: var(--primary-color);
            margin-right: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .test-item {
            background: #f8f9fa;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .included-tests {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <!-- Package Details Section -->
    <div class="package-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($package['package_name']); ?></h1>
                    <p class="lead mb-0">Comprehensive health package with multiple diagnostic tests</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="price-tag">₹<?php echo $package['discounted_price']; ?></div>
                    <?php if($off > 0): ?>
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-2">
                            <span class="original-price fs-4">₹<?php echo $package['original_price']; ?></span>
                            <span class="discount-badge"><?php echo $off; ?>% OFF</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card package-card p-4 mb-4">
                    <h3 class="fw-bold mb-4">Package Overview</h3>
                    <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($package['description'] ?? 'Comprehensive health package for complete body checkup and preventive healthcare.')); ?></p>
                    
                    <?php if(!empty($package['preparation_instructions'])): ?>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-clipboard-list text-primary"></i> Preparation Instructions</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($package['preparation_instructions'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tests Included -->
                <div class="card package-card p-4 mb-4">
                    <h4 class="fw-bold mb-4"><i class="fas fa-vial text-primary"></i> Tests Included</h4>
                    <div class="included-tests">
                        <?php if(!empty($package['tests_included'])): ?>
                            <?php 
                            $tests = explode("\n", $package['tests_included']);
                            foreach($tests as $test): 
                                if(trim($test)): 
                            ?>
                                <div class="test-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <?php echo htmlspecialchars(trim($test)); ?>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        <?php else: ?>
                            <p class="text-muted">Detailed test list will be provided after booking.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Package Features -->
                <div class="card package-card p-4 mb-4">
                    <h4 class="fw-bold mb-4">Package Benefits</h4>
                    <ul class="feature-list">
                        <li><i class="fas fa-home"></i> <strong>Free Home Collection:</strong> Convenient sample collection at your doorstep</li>
                        <li><i class="fas fa-clock"></i> <strong>Report Time:</strong> <?php echo htmlspecialchars($package['report_time'] ?? '24-48 Hours'); ?></li>
                        <li><i class="fas fa-vial"></i> <strong>Multiple Tests:</strong> Comprehensive health assessment in one package</li>
                        <li><i class="fas fa-shield-alt"></i> <strong>Cost Effective:</strong> Save up to <?php echo $off; ?>% compared to individual tests</li>
                        <li><i class="fas fa-user-md"></i> <strong>Doctor Consultation:</strong> Free consultation with health report analysis</li>
                        <li><i class="fas fa-chart-line"></i> <strong>Health Trends:</strong> Track your health parameters over time</li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Booking Card -->
                <div class="card package-card p-4 mb-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-4">Book This Package</h4>
                    
                    <div class="mb-4">
                        <div class="price-tag mb-2">₹<?php echo $package['discounted_price']; ?></div>
                        <?php if($off > 0): ?>
                            <div class="d-flex align-items-center gap-3">
                                <span class="original-price text-dark">₹<?php echo $package['original_price']; ?></span>
                                <span class="discount-badge">Save ₹<?php echo $package['original_price'] - $package['discounted_price']; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="addtocart.php?id=<?php echo $package_id; ?>&type=package" class="btn btn-primary btn-lg">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </a>
                        <a href="bookpackage.php?id=<?php echo $package_id; ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-calendar-check me-2"></i>Book Now
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Package Includes:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>All Listed Tests</li>
                            <li><i class="fas fa-check text-success me-2"></i>Free Home Collection</li>
                            <li><i class="fas fa-check text-success me-2"></i>Digital Report</li>
                            <li><i class="fas fa-check text-success me-2"></i>Doctor Consultation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Lifetime Report Storage</li>
                            <li><i class="fas fa-check text-success me-2"></i>Health Risk Assessment</li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            This package includes <?php echo count(explode("\n", $package['tests_included'])); ?>+ tests
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Packages -->
        <?php if($related_result->num_rows > 0): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold mb-4">Related Packages</h3>
                <div class="row">
                    <?php while($related_package = $related_result->fetch_assoc()): 
                        $related_off = 0;
                        if($related_package['original_price'] > 0) {
                            $related_off = round((($related_package['original_price'] - $related_package['discounted_price']) / $related_package['original_price']) * 100);
                        }
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card package-card p-3 h-100">
                            <img src="uploads/packages/<?php echo $related_package['image']; ?>" 
                                 class="card-img-top mb-2" 
                                 style="height:180px; object-fit:cover; border-radius:10px;"
                                 alt="<?php echo htmlspecialchars($related_package['package_name']); ?>">
                            <h6 class="fw-bold"><?php echo htmlspecialchars($related_package['package_name']); ?></h6>
                            <p class="text-muted small">Complete Health Checkup</p>
                            <div class="mt-auto">
                                <p class="fw-bold mb-2">
                                    ₹<?php echo $related_package['discounted_price']; ?>
                                    <?php if($related_off > 0): ?>
                                        <span class="text-decoration-line-through text-muted small">₹<?php echo $related_package['original_price']; ?></span>
                                        <span class="text-success small"><?php echo $related_off; ?>% off</span>
                                    <?php endif; ?>
                                </p>
                                <a href="viewpackage.php?id=<?php echo $related_package['id']; ?>" class="btn btn-outline-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

   <?php include_once "footer.php";?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once "dbcon.php";
include_once "navbar.php";
// Check if test ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: alltests.php");
    exit();
}

$test_id = $_GET['id'];

// Fetch test details from database
$qry = "SELECT * FROM tests WHERE id = ?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    header("Location: alltests.php");
    exit();
}

$test = $result->fetch_assoc();

// Calculate discount percentage
$off = 0;
if($test['original_price'] > 0) {
    $off = round((($test['original_price'] - $test['discounted_price']) / $test['original_price']) * 100);
}

// Fetch related tests (excluding current test)
$related_qry = "SELECT id, test_name, original_price, discounted_price, image FROM tests WHERE id != ? LIMIT 4";
$related_stmt = $conn->prepare($related_qry);
$related_stmt->bind_param("i", $test_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($test['test_name']); ?> - QUICKLAB</title>
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
        
        .test-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .test-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .test-card:hover {
            transform: translateY(-5px);
        }
        
        .price-tag {
            font-size: 2.5rem;
            font-weight: bold;
            color: red;
        }
        
        .original-price {
            color: #282b2eff;
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
        
        .specimen-badge {
            background: #e9ecef;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            margin: 0.2rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Test Details Section -->
    <div class="test-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($test['test_name']); ?></h1>
                    <p class="lead mb-0">Comprehensive diagnostic test with accurate results</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="price-tag">₹<?php echo $test['discounted_price']; ?></div>
                    <?php if($off > 0): ?>
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-2">
                            <span class="original-price fs-4">₹<?php echo $test['original_price']; ?></span>
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
                <div class="card test-card p-4 mb-4">
                    <h3 class="fw-bold mb-4">Test Overview</h3>
                    <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($test['description'] ?? 'Comprehensive diagnostic test for accurate health assessment.')); ?></p>
                    
                    <?php if(!empty($test['preparation_instructions'])): ?>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-clipboard-list text-primary"></i> Preparation Instructions</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($test['preparation_instructions'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($test['test_parameters'])): ?>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-flask text-primary"></i> Test Parameters</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($test['test_parameters'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Test Features -->
                <div class="card test-card p-4 mb-4">
                    <h4 class="fw-bold mb-4">Key Features</h4>
                    <ul class="feature-list">
                        <li><i class="fas fa-home"></i> <strong>Home Collection:</strong> Available at your convenience</li>
                        <li><i class="fas fa-clock"></i> <strong>Report Time:</strong> <?php echo htmlspecialchars($test['report_time'] ?? '24-48 Hours'); ?></li>
                        <li><i class="fas fa-vial"></i> <strong>Sample Type:</strong> <?php echo htmlspecialchars($test['sample_type'] ?? 'Blood'); ?></li>
                        <li><i class="fas fa-shield-alt"></i> <strong>Accuracy:</strong> High precision results guaranteed</li>
                        <li><i class="fas fa-user-md"></i> <strong>Expert Analysis:</strong> Reviewed by certified professionals</li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Booking Card -->
                <div class="card test-card p-4 mb-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-4">Book This Test</h4>
                    
                    <div class="mb-4">
                        <div class="price-tag mb-2">₹<?php echo $test['discounted_price']; ?></div>
                        <?php if($off > 0): ?>
                            <div class="d-flex align-items-center gap-3">
                                <span class="original-price">₹<?php echo $test['original_price']; ?></span>
                                <span class="discount-badge">Save ₹<?php echo $test['original_price'] - $test['discounted_price']; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="addtocart.php?id=<?php echo $test_id; ?>&type=test" class="btn btn-primary btn-lg">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </a>
                        <a href="booktest.php?id=<?php echo $test_id; ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-calendar-check me-2"></i>Book Now
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Test Includes:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Free Home Collection</li>
                            <li><i class="fas fa-check text-success me-2"></i>Digital Report</li>
                            <li><i class="fas fa-check text-success me-2"></i>Doctor Consultation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Lifetime Report Storage</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Tests -->
        <?php if($related_result->num_rows > 0): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold mb-4">Related Tests</h3>
                <div class="row">
                    <?php while($related_test = $related_result->fetch_assoc()): 
                        $related_off = 0;
                        if($related_test['original_price'] > 0) {
                            $related_off = round((($related_test['original_price'] - $related_test['discounted_price']) / $related_test['original_price']) * 100);
                        }
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card test-card p-3 h-100">
                            <img src="images/testimages/<?php echo $related_test['image']; ?>" 
                                 class="card-img-top mb-2" 
                                 style="height:180px; object-fit:cover; border-radius:10px;"
                                 alt="<?php echo htmlspecialchars($related_test['test_name']); ?>">
                            <h6 class="fw-bold"><?php echo htmlspecialchars($related_test['test_name']); ?></h6>
                            <p class="text-muted small">Home Collection Available</p>
                            <div class="mt-auto">
                                <p class="fw-bold mb-2">
                                    ₹<?php echo $related_test['discounted_price']; ?>
                                    <?php if($related_off > 0): ?>
                                        <span class="text-decoration-line-through text-muted small">₹<?php echo $related_test['original_price']; ?></span>
                                        <span class="text-success small"><?php echo $related_off; ?>% off</span>
                                    <?php endif; ?>
                                </p>
                                <a href="viewtest.php?id=<?php echo $related_test['id']; ?>" class="btn btn-outline-primary w-100">View Details</a>
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
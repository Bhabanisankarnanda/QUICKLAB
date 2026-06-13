<?php
session_start();
require_once "navbar.php";
include_once "dbcon.php";

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $package_id = $_POST['package_id'];
    
    // Fetch package details
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();
    
    if ($package) {
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if package already exists in cart
        $item_exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $package_id && $item['type'] == 'package') {
                $item['quantity'] += 1;
                $item_exists = true;
                break;
            }
        }
        
        // If not exists, add new item
        if (!$item_exists) {
            $cart_item = [
                'id' => $package['id'],
                'name' => $package['package_name'],
                'price' => $package['discounted_price'],
                'quantity' => 1,
                'type' => 'package',
                'image' => $package['image']
            ];
            $_SESSION['cart'][] = $cart_item;
        }
        
        $_SESSION['success'] = "Package added to cart successfully!";
    } else {
        $_SESSION['error'] = "Package not found!";
    }
    
    // Redirect to prevent form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$qry = "SELECT * FROM packages";
$stmt = $conn->prepare($qry);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Packages - QuickLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .package-card {
            padding: 22px;
            border-radius: 18px;
            transition: 0.3s;
            min-height: 360px;
        }
        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .package-img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="fw-bold text-center mb-4">All Recommended Packages</h2>

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

    <div class="row">
        <?php while($row = $result->fetch_assoc()) { ?>
            <div class="col-md-3 mb-4">
                <div class="card package-card p-3">
                    <img src="images/packageimages/<?php echo $row['image']; ?>" class="package-img" alt="<?php echo htmlspecialchars($row['package_name']); ?>">

                    <h5 class="fw-bold"><?php echo htmlspecialchars($row['package_name']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($row['description']); ?></p>

                    <p class="fw-bold fs-4">
                        ₹<?php echo $row['discounted_price']; ?>
                        <span class="text-decoration-line-through fs-6 text-muted">
                            ₹<?php echo $row['original_price']; ?>
                        </span>
                        <span class="text-success fs-6">
                            <?php 
                                echo round((($row['original_price'] - $row['discounted_price']) / $row['original_price']) * 100);
                            ?>% off
                        </span>
                    </p>

                    <button class="btn btn-outline-secondary btn-sm mt-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#details<?php echo $row['id']; ?>">
                        View Details
                    </button>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="package_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary w-100 mt-2">
                            Add to Cart
                        </button>
                    </form>

                </div>
            </div>

            <!-- DETAILS MODAL -->
            <div class="modal fade" id="details<?php echo $row['id']; ?>">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-3">
                        <div class="modal-header">
                            <h4 class="fw-bold"><?php echo htmlspecialchars($row['package_name']); ?></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <h6>Tests Included:</h6>
                            <p><?php echo nl2br(htmlspecialchars($row['tests_included'])); ?></p>
                            
                            <div class="mt-3">
                                <strong>Price: </strong>
                                <span class="fs-5">₹<?php echo $row['discounted_price']; ?></span>
                                <span class="text-decoration-line-through text-muted">₹<?php echo $row['original_price']; ?></span>
                                <span class="text-success">
                                    (<?php echo round((($row['original_price'] - $row['discounted_price']) / $row['original_price']) * 100); ?>% off)
                                </span>
                            </div>
                            
                            <form method="POST" action="" class="mt-3">
                                <input type="hidden" name="package_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary w-100">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

</div>
<?php include_once "footer.php";?>
</body>
</html>
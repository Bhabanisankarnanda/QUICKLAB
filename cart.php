<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "dbcon.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Shopping Cart";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cart-item {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .quantity-btn {
            width: 35px;
            height: 35px;
        }
    </style>
</head>
<body>
    <!-- Include your navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="fw-bold mb-4">Shopping Cart</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="card p-4 text-center">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4>Your cart is empty</h4>
                        <p class="text-muted">Add some tests to get started</p>
                        <a href="alltests.php" class="btn btn-primary">Browse Tests</a>
                    </div>
                <?php else: ?>
                    <?php 
                    $total_amount = 0;
                    foreach ($_SESSION['cart'] as $index => $item): 
                        $total_amount += $item['price'] * $item['quantity'];
                    ?>
                    <div class="card cart-item p-3">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="images/testimages/<?php echo $item['image']; ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="height: 80px; object-fit: cover;">
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted"><?php echo ucfirst($item['type']); ?></small>
                            </div>
                            <div class="col-md-2">
                                <span class="fw-bold">₹<?php echo $item['price']; ?></span>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <a href="updatecart.php?action=decrease&index=<?php echo $index; ?>" 
                                       class="btn btn-outline-secondary quantity-btn">-</a>
                                    <span class="mx-3 fw-bold"><?php echo $item['quantity']; ?></span>
                                    <a href="updatecart.php?action=increase&index=<?php echo $index; ?>" 
                                       class="btn btn-outline-secondary quantity-btn">+</a>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="removefromcart.php?index=<?php echo $index; ?>" 
                                   class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Checkout button moved outside the loop -->
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="checkout.php" class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Proceed to Checkout
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <?php if (!empty($_SESSION['cart'])): ?>
                <div class="card p-4">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₹<?php echo $total_amount; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Home Collection:</span>
                        <span class="text-success">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total Amount:</strong>
                        <strong>₹<?php echo $total_amount; ?></strong>
                    </div>
                    <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                    <a href="alltests.php" class="btn btn-outline-primary w-100 mt-2">Continue Shopping</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if($_POST) {
    $booking_date = $_POST['booking_date'];
    $time_slot = $_POST['time_slot'];
    $address = $_POST['address'];
    
    // Insert each cart item as a booking
    foreach($_SESSION['cart'] as $item) {
        $test_id = NULL;
        $package_id = NULL;
        
        if($item['type'] == 'test') {
            $test_id = $item['id'];
        } else {
            $package_id = $item['id'];
        }
        
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, test_id, package_id, type, item_name, price, booking_date, time_slot, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissdsss", $user_id, $test_id, $package_id, $item['type'], $item['name'], $item['price'], $booking_date, $time_slot, $address);
        $stmt->execute();
    }
    
    // Clear cart after successful booking
    $_SESSION['cart'] = [];
    $_SESSION['cart_count'] = 0;
    
    $_SESSION['success'] = "Booking confirmed successfully!";
    header("Location: my_bookings.php");
    exit();
}

// Calculate total amount
$total_amount = 0;
foreach($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .checkout-card { border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .cart-item { border-bottom: 1px solid #e9ecef; padding: 15px 0; }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card checkout-card p-4">
                    <h3 class="mb-4">Checkout</h3>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Booking Date</label>
                                    <input type="date" name="booking_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Preferred Time Slot</label>
                                    <select name="time_slot" class="form-select" required>
                                        <option value="">Select Time Slot</option>
                                        <option value="Morning (7 AM - 8 AM)">Morning (7 AM - 8 AM)</option>
                                        <option value="Morning (8 AM - 9 AM)">Morning (8 AM - 9 AM)</option>
                                        <option value="Morning (9 AM - 10 AM)">Morning (9 AM - 10 AM)</option>
                                         <option value="Morning (10 AM - 11 AM)">Morning (10 AM - 11 AM)</option>
                                         <option value="Morning (11 AM - 12 AM)">Morning (11 AM - 12 PM)</option>
                                         <option value="Afternoon (4 PM - 5 PM)">Afternoon (4 PM - 5 PM)</option>
                                        <option value="Evening (5 PM - 7 PM)">Evening (5 PM - 7 PM)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Collection Address</label>
                            <textarea name="address" class="form-control" rows="4" placeholder="Enter complete address for sample collection" required></textarea>
                        </div>
                        
                        <h5 class="mb-3">Order Summary</h5>
                        <?php foreach($_SESSION['cart'] as $item): ?>
                        <div class="cart-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                    <small class="text-muted"><?php echo ucfirst($item['type']); ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">₹<?php echo $item['price']; ?></div>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="d-flex justify-content-between mt-3 fw-bold fs-5">
                            <span>Total Amount:</span>
                            <span>₹<?php echo $total_amount; ?></span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">
                            <i class="fas fa-calendar-check me-2"></i>Confirm Booking
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card checkout-card p-4">
                    <h5 class="mb-3">Booking Information</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Home Collection Service</strong>
                        <p class="mb-0 mt-2">Our trained phlebotomist will visit your specified address for sample collection.</p>
                    </div>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free home collection</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Professional staff</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Safe sample handling</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Timely reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
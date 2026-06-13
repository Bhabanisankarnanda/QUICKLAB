<?php
session_start();
require_once "dbcon.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$bookings = $conn->query("
    SELECT b.*, 
           COALESCE(t.test_name, p.package_name) as item_name
    FROM bookings b
    LEFT JOIN tests t ON b.test_id = t.id AND b.type = 'test'
    LEFT JOIN packages p ON b.package_id = p.id AND b.type = 'package'
    WHERE b.user_id = $user_id 
    ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">My Bookings</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if($bookings->num_rows > 0): ?>
            <div class="row">
                <?php while($booking = $bookings->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $booking['item_name']; ?></h5>
                            <p class="card-text">
                                <strong>Date:</strong> <?php echo $booking['booking_date']; ?><br>
                                <strong>Time:</strong> <?php echo $booking['time_slot']; ?><br>
                                <strong>Amount:</strong> ₹<?php echo $booking['price']; ?><br>
                                <strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    switch($booking['status']) {
                                        case 'confirmed': echo 'success'; break;
                                        case 'pending': echo 'warning'; break;
                                        case 'completed': echo 'info'; break;
                                        case 'cancelled': echo 'danger'; break;
                                        default: echo 'secondary';
                                    }
                                ?>"><?php echo ucfirst($booking['status']); ?></span>
                            </p>
                            <p class="card-text"><small class="text-muted">Booked on: <?php echo $booking['created_at']; ?></small></p>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4>No bookings found</h4>
                <p class="text-muted">You haven't made any bookings yet.</p>
                <a href="alltests.php" class="btn btn-primary">Book a Test</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
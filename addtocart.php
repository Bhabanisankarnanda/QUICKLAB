<?php
session_start();
require_once "dbcon.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_redirect'] = "Please login to add items to cart";
    header("Location: login.php");
    exit();
}

// Check if required parameters are provided
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    $_SESSION['error'] = "Invalid request";
    header("Location: alltests.php");
    exit();
}

$item_id = $_GET['id'];
$item_type = $_GET['type']; // 'test' or 'package'
$user_id = $_SESSION['user_id'];

// Validate item type
if ($item_type !== 'test' && $item_type !== 'package') {
    $_SESSION['error'] = "Invalid item type";
    header("Location: alltests.php");
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if item already exists in cart
$item_exists = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $item_id && $item['type'] == $item_type) {
        $item['quantity'] += 1;
        $item_exists = true;
        break;
    }
}

// If item doesn't exist, fetch details and add to cart
if (!$item_exists) {
    if ($item_type == 'test') {
        // Fetch test details
        $stmt = $conn->prepare("SELECT id, test_name, discounted_price, image FROM tests WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $test = $result->fetch_assoc();
            
            $cart_item = [
                'id' => $test['id'],
                'name' => $test['test_name'],
                'price' => $test['discounted_price'],
                'type' => 'test',
                'quantity' => 1,
                'image' => $test['image']
            ];
            
            $_SESSION['cart'][] = $cart_item;
            $_SESSION['success'] = "Test added to cart successfully!";
        } else {
            $_SESSION['error'] = "Test not found";
        }
    } else {
        // For packages (if you have them)
        $stmt = $conn->prepare("SELECT id, package_name, discounted_price, image FROM packages WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $package = $result->fetch_assoc();
            
            $cart_item = [
                'id' => $package['id'],
                'name' => $package['package_name'],
                'price' => $package['discounted_price'],
                'type' => 'package',
                'quantity' => 1,
                'image' => $package['image']
            ];
            
            $_SESSION['cart'][] = $cart_item;
            $_SESSION['success'] = "Package added to cart successfully!";
        } else {
            $_SESSION['error'] = "Package not found";
        }
    }
} else {
    $_SESSION['success'] = "Item quantity updated in cart!";
}

// Update cart count in session
$_SESSION['cart_count'] = count($_SESSION['cart']);

// Redirect back to previous page or cart
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: cart.php");
}
exit();
?>
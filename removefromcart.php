<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if index parameter is provided
if (isset($_GET['index'])) {
    $index = (int)$_GET['index'];
    
    // Check if the cart exists and the index is valid
    if (isset($_SESSION['cart'][$index])) {
        // Remove the item from cart
        unset($_SESSION['cart'][$index]);
        
        // Reindex the array to avoid gaps
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        // Set success message
        $_SESSION['success'] = "Item removed from cart successfully!";
    } else {
        $_SESSION['error'] = "Invalid item selected!";
    }
} else {
    $_SESSION['error'] = "No item selected for removal!";
}

// Redirect back to cart page
header("Location: cart.php");
exit();
?>
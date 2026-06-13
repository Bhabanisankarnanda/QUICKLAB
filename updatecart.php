<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['action']) || !isset($_GET['index'])) {
    header("Location: cart.php");
    exit();
}

$action = $_GET['action'];
$index = $_GET['index'];

if (isset($_SESSION['cart'][$index])) {
    if ($action == 'increase') {
        $_SESSION['cart'][$index]['quantity'] += 1;
    } elseif ($action == 'decrease') {
        if ($_SESSION['cart'][$index]['quantity'] > 1) {
            $_SESSION['cart'][$index]['quantity'] -= 1;
        } else {
            // Remove item if quantity becomes 0
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        }
    }
    
    // Update cart count
    $_SESSION['cart_count'] = count($_SESSION['cart']);
}

header("Location: cart.php");
exit();
?>
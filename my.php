<?php
require_once "dbcon.php";

// Generate a fresh hash for admin123
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "New Hash: " . $hash . "<br>";

// Update admin password
$sql = "UPDATE admin SET password = '$hash' WHERE email = 'admin@quicklab.com'";

if($conn->query($sql)) {
    echo "Admin password updated successfully!<br>";
    echo "Email: admin@quicklab.com<br>";
    echo "Password: admin123<br>";
    
    // Verify the new hash
    if(password_verify('admin123', $hash)) {
        echo "✓ Password verification successful!";
    } else {
        echo "✗ Password verification failed!";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
<?php
session_start();
require_once "dbcon.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    echo "<pre>Debug Info:\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    
    // Check if admin exists
    $stmt = $conn->prepare("SELECT id, name, password FROM admin WHERE email = ?");
    if($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            echo "Admin found: " . $admin['name'] . "\n";
            echo "Stored hash: " . $admin['password'] . "\n";
            
            // Verify password
            if(password_verify($password, $admin['password'])) {
                echo "Password verified successfully!\n";
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                echo "Redirecting to dashboard...\n";
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Password verification failed!\n";
                $error = "Invalid password!";
            }
        } else {
            echo "No admin found with this email!\n";
            $error = "Admin not found!";
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error . "\n";
        $error = "Database error!";
    }
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card shadow p-4">
            <h3 class="text-center mb-4">Admin Login</h3>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="admin@quicklab.com" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="admin123" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <div class="text-center mt-3">
                <small class="text-muted">Default credentials pre-filled</small>
            </div>
        </div>
    </div>
</body>
</html>
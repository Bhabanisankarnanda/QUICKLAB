<?php
session_start();
require_once "dbcon.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Fetch user details
$user_qry = "SELECT * FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_qry);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    header("Location: logout.php");
    exit();
}

$user = $user_result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        // Check if email already exists (excluding current user)
        $email_check = "SELECT id FROM users WHERE email = ? AND id != ?";
        $email_stmt = $conn->prepare($email_check);
        $email_stmt->bind_param("si", $email, $user_id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();

        if ($email_result->num_rows > 0) {
            $error_msg = "Email already exists. Please use a different email.";
        } else {
            // Update user profile
            $update_qry = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, gender = ?, age = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_qry);
            $update_stmt->bind_param("sssssii", $name, $email, $phone, $address, $gender, $age, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $success_msg = "Profile updated successfully!";
                
                // Refresh user data
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $user = $user_result->fetch_assoc();
            } else {
                $error_msg = "Error updating profile. Please try again.";
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $error_msg = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error_msg = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_qry = "UPDATE users SET password = ? WHERE id = ?";
        $password_stmt = $conn->prepare($password_qry);
        $password_stmt->bind_param("si", $hashed_password, $user_id);

        if ($password_stmt->execute()) {
            $success_msg = "Password changed successfully!";
        } else {
            $error_msg = "Error changing password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - QUICKLAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 2rem;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
            border: 5px solid white;
        }
        
        .nav-pills .nav-link {
            color: #495057;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
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
        
        .stats-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'navbar.php'; ?>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start">
                    <h1 class="display-6 fw-bold">My Profile</h1>
                    <p class="lead mb-0">Manage your account information and preferences</p>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Success/Error Messages -->
        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 mb-4">
                <div class="card profile-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="profile-avatar mb-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        
                        <nav class="nav nav-pills flex-column">
                            <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                                <i class="fas fa-user-edit me-2"></i>Profile Information
                            </a>
                            <a class="nav-link" href="#password" data-bs-toggle="tab">
                                <i class="fas fa-lock me-2"></i>Change Password
                            </a>
                            <a class="nav-link" href="#bookings" data-bs-toggle="tab">
                                <i class="fas fa-calendar-check me-2"></i>My Bookings
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card profile-card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Quick Stats</h6>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="stats-card">
                                    <div class="stats-number">
                                        <?php 
                                        $booking_count = $conn->query("SELECT COUNT(*) FROM bookings WHERE user_id = $user_id")->fetch_row()[0];
                                        echo $booking_count;
                                        ?>
                                    </div>
                                    <div class="stats-label">Total Bookings</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stats-card">
                                    <div class="stats-number">
                                        <?php 
                                        $completed_count = $conn->query("SELECT COUNT(*) FROM bookings WHERE user_id = $user_id AND status = 'completed'")->fetch_row()[0];
                                        echo $completed_count;
                                        ?>
                                    </div>
                                    <div class="stats-label">Tests Completed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Profile Information Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card profile-card">
                            <div class="card-header bg-white">
                                <h5 class="fw-bold mb-0"><i class="fas fa-user-edit me-2"></i>Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Full Name</label>
                                            <input type="text" class="form-control" name="name" 
                                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Email Address</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" 
                                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Gender</label>
                                            <select class="form-control" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" <?php echo (isset($user['gender']) && $user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="female" <?php echo (isset($user['gender']) && $user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                                <option value="other" <?php echo (isset($user['gender']) && $user['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Age</label>
                                            <input type="number" class="form-control" name="age" 
                                                   value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>" min="1" max="120">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Address</label>
                                            <textarea class="form-control" name="address" rows="3" 
                                                      placeholder="Enter your complete address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="password">
                        <div class="card profile-card">
                            <div class="card-header bg-white">
                                <h5 class="fw-bold mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">New Password</label>
                                            <input type="password" class="form-control" name="new_password" required minlength="6">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password" required minlength="6">
                                        </div>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i>Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- My Bookings Tab -->
                    <div class="tab-pane fade" id="bookings">
                        <div class="card profile-card">
                            <div class="card-header bg-white">
                                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-check me-2"></i>My Bookings</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $bookings_qry = "SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC LIMIT 5";
                                $bookings_stmt = $conn->prepare($bookings_qry);
                                $bookings_stmt->bind_param("i", $user_id);
                                $bookings_stmt->execute();
                                $bookings_result = $bookings_stmt->get_result();
                                ?>
                                
                                <?php if ($bookings_result->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td>#QL<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            switch($booking['status']) {
                                                                case 'completed': echo 'success'; break;
                                                                case 'pending': echo 'warning'; break;
                                                                case 'processing': echo 'info'; break;
                                                                case 'cancelled': echo 'danger'; break;
                                                                default: echo 'secondary';
                                                            }
                                                        ?>">
                                                            <?php echo ucfirst($booking['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="download_report.php" class="btn btn-sm btn-outline-primary">
                                                            View Details
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="download_report.php" class="btn btn-primary">View All Bookings</a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Bookings Yet</h5>
                                        <p class="text-muted">You haven't made any bookings yet.</p>
                                        <a href="alltests.php" class="btn btn-primary">Book a Test</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include_once "footer.php";?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activate tab based on URL hash
        document.addEventListener('DOMContentLoaded', function() {
            var triggerTabList = [].slice.call(document.querySelectorAll('a[data-bs-toggle="tab"]'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            });
        });
    </script>
</body>
</html>
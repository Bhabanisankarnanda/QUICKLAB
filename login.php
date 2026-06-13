<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>QuickLab - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #f5f8ff 0%, #e3e9ff 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .navbar-brand {
            font-size: 22px;
        }
        
        .card { 
            border-radius: 15px; 
            padding: 30px; 
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary { 
            border-radius: 10px; 
            font-weight: bold;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            padding: 12px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        .form-control { 
            border-radius: 10px; 
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        .login-container {
            margin-top: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .create-account-link {
            color: #4361ee;
            text-decoration: none;
            font-weight: 600;
        }
        
        .create-account-link:hover {
            text-decoration: underline;
            color: #3a0ca3;
        }
        
        .error-message {
            background: #ffe6e6;
            border: 1px solid #ff3333;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container">
            <!-- LEFT: LOGO ONLY -->
            <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="index.php">
                <img src="./assets/logo.jpeg" width="55" class="me-2" style="border-radius:8px;">
                QUICKLAB DIAGNOSTICS
            </a>

            <!-- RIGHT LINKS -->
            <ul class="navbar-nav ms-auto flex-row">
                <!-- INACTIVE LINK -->
                <li class="nav-item me-3">
                    <a class="nav-link disabled fw-semibold text-secondary" tabindex="-1" aria-disabled="true">
                        Login Page
                    </a>
                </li>

                <!-- ACTIVE LINKS -->
                <li class="nav-item me-3">
                    <a class="nav-link fw-semibold" href="packages.php">Health Packages</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="booktest.php">Book a Test</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- LOGIN FORM -->
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Welcome Back</h3>
                        <p class="text-muted">Log in to your account</p>
                    </div>

                    <?php
                    if(isset($_SESSION['login_error'])){
                        echo '<div class="error-message text-center">'.$_SESSION['login_error'].'</div>';
                        unset($_SESSION['login_error']);
                    }
                    ?>

                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email or Phone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" name="login_id" class="form-control border-start-0" placeholder="Enter email or phone number" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>

                        <p class="text-center mt-4 mb-0">
                            New here? 
                            <a href="register.php" class="create-account-link">Create Account</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
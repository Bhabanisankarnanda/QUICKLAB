<?php
session_start();
require_once "dbcon.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
<style> 
    :root {
    --primary-color: #4361ee;
    --secondary-color: #3a0ca3;
    --accent-color: #4cc9f0;
    --light-bg: #f8f9fa;
    --dark-text: #212529;
    --success-color: #4bb543;
    --error-color: #ff3333;
}

body {
    background: linear-gradient(135deg, #eef2ff 0%, #c7d6ff 100%);
    min-height: 100vh;
    padding: 0;   /* Remove top & bottom gap */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

   .navbar {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .navbar-brand {
            font-size: 22px;
        }
        
        .nav-link {
            padding: 0.5rem 1rem;
            transition: color 0.3s;
        }
        
        .nav-link:not(.disabled):hover {
            color: var(--primary-color) !important;
        }
/* ------------------ MAIN CONTAINER ------------------ */
.signup-container {
    max-width: 950px;
    border-radius: 25px;
    overflow: hidden;
    background: white;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
}
.signup-container {
    margin-top: 35px;
}


/* ------------------ LEFT SECTION ------------------ */
.signup-left {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 60px 40px;
    min-height: 100%;
}

.welcome-text {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1.3;
}

.feature-list li {
    font-size: 1.05rem;
}

/* ------------------ RIGHT FORM SECTION ------------------ */
.signup-right {
    padding: 50px 45px;
}

/* Header */
.form-header h2 {
    font-size: 2rem;
    font-weight: 800;
}

.form-header p {
    font-size: 0.95rem;
}

/* ------------------ FORM ELEMENTS ------------------ */

/* Input groups spacing */
.mb-3 {
    margin-bottom: 1.4rem !important;
}

/* Input fields */
.form-control, .form-select, textarea {
    padding: 14px 16px !important;
    border: 2px solid #e4e7ed;
    border-radius: 12px !important;
    font-size: 15px;
    background: #f9faff;
    transition: all 0.3s ease;
}

/* On focus */
.form-control:focus, .form-select:focus, textarea:focus {
    border-color: var(--primary-color);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
}

/* Textarea styling */
textarea {
    min-height: 85px;
    resize: none;
}

/* Input group icons */
.input-group-text {
    background: #eef1ff;
    border: none;
    border-radius: 12px 0 0 12px !important;
    padding: 14px 18px !important;
}

/* ------------------ PASSWORD TOGGLE BUTTON ------------------ */
#togglePassword {
    border-radius: 0 12px 12px 0 !important;
    padding: 0 18px;
}

/* ------------------ PASSWORD STRENGTH BAR ------------------ */
.password-strength {
    height: 7px;
    margin-top: 8px;
    border-radius: 10px;
}

/* ------------------ SUBMIT BUTTON ------------------ */
.btn-primary {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    border-radius: 12px;
    padding: 14px;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: .5px;
    transition: all .3s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(67, 97, 238, 0.45);
}

/* ------------------ LOGIN LINK ------------------ */
.login-link {
    margin-top: 20px;
    text-align: center;
}

.login-link a {
    font-weight: 700;
    font-size: 1rem;
}

/* ------------------ RESPONSIVE FIX ------------------ */
@media (max-width: 768px) {
    .signup-left {
        padding: 40px 25px;
        text-align: center;
    }
    .signup-right {
        padding: 40px 25px;
    }
    .welcome-text {
        font-size: 1.8rem;
    }
    .signup-container {
        margin-top: 20px;
    }
}
</style>
 
</head>

<body>
 <!-- HEADER (No Gap at Top) -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">

            <!-- LEFT: LOGO ONLY -->
            <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="index.php">
                <img src="./assets/logo.jpeg" width="55" class="me-2" style="border-radius:8px;">
                QUICKLAB DIAGNOSTICS
            </a>

            <!-- RIGHT LINKS -->
            <ul class="navbar-nav ms-auto">

                <!-- INACTIVE LINK -->
                <li class="nav-item">
                    <a class="nav-link disabled fw-semibold text-secondary" tabindex="-1" aria-disabled="true">
                       You are in the  <br>Registration Page
                    </a>
                </li>

                <!-- ACTIVE LINKS -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="allpackages.php">Health Packages</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="./alltests.php">Book a Test</a>
                </li>

            </ul>

        </div>
    </nav>

    <div class="container signup-container">
        <div class="row g-0">
            <div class="col-md-5 signup-left">
    <div class="welcome-text">Welcome to QuickLab</div>
    <br>
    <p>Your trusted partner for medical diagnostics. Create your account to book tests instantly and enjoy seamless healthcare services.</p>
     <br>
    <ul class="feature-list">
        <li><i class="fas fa-check-circle"></i> Book Tests in Seconds</li>
        <li><i class="fas fa-check-circle"></i> Home Sample Collection</li>
        <li><i class="fas fa-check-circle"></i> Accurate & Trusted Reports</li>
        <li><i class="fas fa-check-circle"></i> Track Reports Anytime</li>
        <li><i class="fas fa-check-circle"></i> 24/7 Health Support</li>
        <li><i class="fas fa-check-circle"></i> Exclusive Discounts on Packages</li>
        <li><i class="fas fa-check-circle"></i>NABL-Approved Diagnostic Quality</li>

    </ul>
</div>

            
            
            <div class="col-md-7 signup-right">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Fill in your details to get started</p>
                </div>

                <?php 
                if(isset($_SESSION['msg'])) {
                    echo '<div class="alert alert-custom alert-info">' . $_SESSION['msg'] . '</div>';
                    unset($_SESSION['msg']);
                }
                ?>

                <form method="POST" action="register_process.php" onsubmit="return validateForm()" autocomplete="off">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name">
                            </div>
                            <small class="form-text text-muted">Minimum 3 characters</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="text" class="form-control" id="email" name="email" placeholder="email@gmail.com">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="10-digit number">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age" min="1" max="120">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" id="gender">
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" id="address" rows="2" placeholder="Enter your full address"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a strong password">
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <small class="form-text text-muted">Must include uppercase, number, symbol, and be at least 6 characters</small>
                    </div>

                    <button class="btn btn-primary w-100 py-2 mt-3">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>

                <div class="login-link">
                    Already have an account? <a href="login.php">Log In</a>
                </div>
            </div>
        </div>
    </div>


    <script>
        // ---------------- PASSWORD VISIBILITY TOGGLE ---------------- //
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // ---------------- PASSWORD STRENGTH INDICATOR ---------------- //
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            // Reset
            strengthBar.className = 'password-strength';
            
            if (password.length === 0) {
                return;
            }
            
            // Calculate strength
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength += 1;
            
            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 1;
            
            // Contains number
            if (/[0-9]/.test(password)) strength += 1;
            
            // Contains special character
            if (/[@$!%*?&]/.test(password)) strength += 1;
            
            // Update strength bar
            if (strength <= 1) {
                strengthBar.classList.add('password-weak');
            } else if (strength <= 2) {
                strengthBar.classList.add('password-medium');
            } else {
                strengthBar.classList.add('password-strong');
            }
        });
        
        // ---------------- REGEX VALIDATION ---------------- //
        function validateForm() {
            let name = document.getElementById("name").value.trim();
            let email = document.getElementById("email").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let password = document.getElementById("password").value.trim();
            let age = document.getElementById("age").value;
            let gender = document.getElementById("gender").value;

            let nameReg = /^[A-Za-z ]{3,}$/;
            let emailReg = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;
            let phoneReg = /^[6-9]\d{9}$/;
            let passReg = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;

            if(!nameReg.test(name)) {
                alert("Please enter a valid name (minimum 3 letters, alphabets only)");
                document.getElementById("name").focus();
                return false;
            }

            if(!emailReg.test(email)) {
                alert("Invalid Email Format. Please enter a valid email address.");
                document.getElementById("email").focus();
                return false;
            }

            if(!phoneReg.test(phone)) {
                alert("Phone number must be 10 digits and start with 6-9");
                document.getElementById("phone").focus();
                return false;
            }
            
            if(age < 1 || age > 120 || age === "") {
                alert("Please enter a valid age between 1 and 120");
                document.getElementById("age").focus();
                return false;
            }
            
            if(gender === "") {
                alert("Please select your gender");
                document.getElementById("gender").focus();
                return false;
            }

            if(!passReg.test(password)) {
                alert("Password must include at least 1 uppercase letter, 1 number, 1 special symbol (@$!%*?&), and be at least 6 characters long");
                document.getElementById("password").focus();
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
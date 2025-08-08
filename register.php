<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register -DULUX </title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .register-page {
            padding-top: 100px;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .register-container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .register-header p {
            color: #666;
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }
        
        .form-group .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .form-group .input-icon:hover {
            color: #e74c3c;
        }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .password-requirements h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .password-requirements li.valid {
            color: #28a745;
        }
        
        .password-requirements li.invalid {
            color: #dc3545;
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .terms-checkbox input[type="checkbox"] {
            width: auto;
            margin: 0;
            margin-top: 0.2rem;
        }
        
        .terms-checkbox label {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
        }
        
        .terms-checkbox a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
        }
        
        .terms-checkbox a:hover {
            color: #c0392b;
        }
        
        .register-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .register-btn:hover::before {
            left: 100%;
        }
        
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        }
        
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e8ed;
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .social-register {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .social-btn {
            padding: 0.8rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            background: white;
            color: #2c3e50;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            border-color: #e74c3c;
            color: #e74c3c;
            transform: translateY(-2px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .login-link a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #c0392b;
        }
        
        .back-btn {
            position: fixed;
            top: 120px;
            left: 2rem;
            background: white;
            color: #2c3e50;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
        }
        
        /* Alert Styles */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .register-container {
                padding: 1rem;
            }
            
            .register-card {
                padding: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .social-register {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <div class="logo-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="logo-text">
                    <h2>DULUX</h2>
                    <span>Luxury Hotel</span>
                </div>
            </a>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="index.php#rooms" class="nav-link">Rooms</a></li>
                <li><a href="index.php#packages" class="nav-link">Packages</a></li>
                <li><a href="index.php#amenities" class="nav-link">Amenities</a></li>
                <li><a href="index.php#contact" class="nav-link">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link">Reservations <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="booking.php"><i class="fas fa-bed"></i> Room Booking</a></li>
                        <li><a href="dining.php"><i class="fas fa-utensils"></i> Dining Reservation</a></li>
                        <li><a href="events.php"><i class="fas fa-glass-cheers"></i> Event Reservation</a></li>
                    </ul>
                </li>
                <li><a href="login.php" class="nav-link">Login</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Back Button -->
    <a href="index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <!-- Register Page -->
    <section class="register-page">
        <div class="register-container">
            <div class="register-card">
                <?php
                // Display success or error messages
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message'] ?? 'Registration successful!') . '</div>';
                }
                if (isset($_GET['error']) && $_GET['error'] == '1') {
                    echo '<div class="alert alert-error">' . htmlspecialchars($_GET['message'] ?? 'Registration failed. Please try again.') . '</div>';
                }
                ?>
                
                <div class="register-header">
                    <h1>Join DULUX</h1>
                    <p>Create your account to access exclusive benefits</p>
                </div>

                <form class="register-form" id="registerForm" action="process_register.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" name="firstname" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" name="lastname" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                        <i class="fas fa-phone input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country" required>
                            <option value="">Select Country</option>
                            <option value="FR">Sri Lanka</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="UK">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                            <option value="IN">India</option>
                            <option value="BR">Brazil</option>
                            <option value="other">Other</option>
                        </select>
                        <i class="fas fa-globe input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-eye input-icon" id="togglePassword"></i>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <i class="fas fa-eye input-icon" id="toggleConfirmPassword"></i>
                    </div>

                    <div class="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul>
                            <li id="length"><i class="fas fa-circle"></i> At least 8 characters</li>
                            <li id="uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                            <li id="lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                            <li id="number"><i class="fas fa-circle"></i> One number</li>
                            <li id="special"><i class="fas fa-circle"></i> One special character</li>
                        </ul>
                    </div>

                    <div class="terms-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">
                            I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="terms-checkbox">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <label for="newsletter">
                            I would like to receive updates and special offers from DULUX
                        </label>
                    </div>

                    <button type="submit" class="register-btn">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>

                <div class="divider">
                    <span>or sign up with</span>
                </div>

                <div class="social-register">
                    <a href="#" class="social-btn">
                        <i class="fab fa-google"></i>
                        Google
                    </a>
                    <a href="#" class="social-btn">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </a>
                </div>

                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
        </div>
    </section>

    <script src="js/script.js"></script>
    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
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

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
            const icon = this;
            
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

        // Password validation
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        function validatePassword(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            // Update visual indicators
            Object.keys(requirements).forEach(req => {
                const element = document.getElementById(req);
                if (requirements[req]) {
                    element.classList.add('valid');
                    element.classList.remove('invalid');
                    element.querySelector('i').className = 'fas fa-check';
                } else {
                    element.classList.add('invalid');
                    element.classList.remove('valid');
                    element.querySelector('i').className = 'fas fa-times';
                }
            });

            return Object.values(requirements).every(Boolean);
        }

        function validateConfirmPassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword && password !== confirmPassword) {
                confirmPasswordInput.setCustomValidity("Passwords don't match");
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        }

        passwordInput.addEventListener('input', function() {
            validatePassword(this.value);
        });

        confirmPasswordInput.addEventListener('input', validateConfirmPassword);
    </script>
</body>
</html> 
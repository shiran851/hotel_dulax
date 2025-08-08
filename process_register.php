<?php
session_start();
require_once 'config/database.php';

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstname = sanitize($_POST['firstname'] ?? '');
    $lastname = sanitize($_POST['lastname'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $country = sanitize($_POST['country'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    $newsletter = isset($_POST['newsletter']);

    // Validate required fields
    $errors = [];
    
    if (empty($firstname)) {
        $errors[] = "First name is required";
    }
    
    if (empty($lastname)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($country)) {
        $errors[] = "Country is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($confirm_password)) {
        $errors[] = "Password confirmation is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Password strength validation
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    if (!$terms) {
        $errors[] = "You must agree to the Terms of Service";
    }

    // If no errors, process the registration
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error_message = "An account with this email already exists.";
                redirectWithMessage('register.php', 'error', $error_message);
            }
            
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $db->prepare("
                INSERT INTO users (firstname, lastname, email, phone, country, password_hash, newsletter_subscription) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $firstname, 
                $lastname, 
                $email, 
                $phone, 
                $country, 
                $password_hash, 
                $newsletter ? 1 : 0
            ]);
            
            $user_id = $db->lastInsertId();
            
            // Log the registration
            logActivity($user_id, 'register', 'New user registered');
            
            // Create success message
            $success_message = "Welcome to DULUX, $firstname! Your account has been created successfully.";
            
            // Redirect back to login page with success message
            redirectWithMessage('login.php', 'success', $success_message);
            
        } catch (PDOException $e) {
            $error_message = "Database error. Please try again later.";
            redirectWithMessage('register.php', 'error', $error_message);
        }
    } else {
        // Redirect back with errors
        $error_message = implode(", ", $errors);
        redirectWithMessage('register.php', 'error', $error_message);
    }
} else {
    // If not POST request, redirect to register page
    header("Location: register.php");
    exit();
}
?> 
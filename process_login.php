<?php
session_start();
require_once 'config/database.php';

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate required fields
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    // If no errors, process the login
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Check if user exists
            $stmt = $db->prepare("SELECT id, firstname, lastname, email, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['logged_in'] = true;
                
                if ($remember) {
                    // Set remember me cookie (30 days)
                    setcookie('remember_user', $user['email'], time() + (30 * 24 * 60 * 60), '/');
                }
                
                // Log the login activity
                logActivity($user['id'], 'login', 'User logged in successfully');
                
                $success_message = "Welcome back, " . $user['firstname'] . "! You have successfully logged in.";
                redirectWithMessage('index.php', 'success', $success_message);
            } else {
                // Login failed
                $error_message = "Invalid email or password. Please try again.";
                redirectWithMessage('login.php', 'error', $error_message);
            }
        } catch (PDOException $e) {
            $error_message = "Database error. Please try again later.";
            redirectWithMessage('login.php', 'error', $error_message);
        }
    } else {
        // Redirect back with errors
        $error_message = implode(", ", $errors);
        redirectWithMessage('login.php', 'error', $error_message);
    }
} else {
    // If not POST request, redirect to login page
    header("Location: login.php");
    exit();
}
?> 
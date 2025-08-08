<?php
session_start();
require_once 'config/database.php';

// Log the logout activity if user is logged in
if (isLoggedIn()) {
    $user_id = getCurrentUserId();
    if ($user_id) {
        logActivity($user_id, 'logout', 'User logged out');
    }
}

// Destroy session
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Redirect to home page with logout message
redirectWithMessage('index.php', 'success', 'You have been successfully logged out.');
?> 
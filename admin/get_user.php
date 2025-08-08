<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, firstname, lastname, email, phone, country FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'User not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?> 
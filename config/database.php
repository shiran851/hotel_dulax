<?php
// Database configuration for DULUX
class Database {
    private $host = 'localhost';
    private $db_name = 'hotel_dulux';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    // Test database connection
    public function testConnection() {
        try {
            $this->getConnection();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}

// Helper functions for database operations
function getDB() {
    $database = new Database();
    return $database->getConnection();
}

// Sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate booking reference
function generateBookingReference() {
    return 'DULUX' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user email
function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

// Redirect with message
function redirectWithMessage($url, $type, $message) {
    header("Location: $url?$type=1&message=" . urlencode($message));
    exit();
}

// Log activity
function logActivity($user_id, $action, $details = '') {
    if (!$user_id) return; // Skip if no user ID
    
    $db = getDB();
    
    // Check if user exists before logging
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    if ($stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $action, $details]);
    }
}


?> 
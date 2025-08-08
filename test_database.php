<?php
// Database Connection Test Script for DULUX
// This script will help diagnose database connection issues

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test - DULUX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: #f8f9fa;
        }
        .test-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .test-item {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 10px;
        }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 0.9rem;
        }
        .btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
        }
    </style>
</head>
<body>
    <div class='test-container'>
        <h1>🔧 Database Connection Test</h1>
        <p>This script will test your database connection and help identify issues.</p>";

// Test 1: Check if config file exists
echo "<div class='test-item info'>
    <h3>1. Configuration File Check</h3>";

if (file_exists('config/database.php')) {
    echo "<p class='success'>✅ config/database.php file exists</p>";
} else {
    echo "<p class='error'>❌ config/database.php file not found</p>";
    echo "<p>Please make sure the config directory and database.php file exist.</p>";
}

echo "</div>";

// Test 2: Check PHP PDO extension
echo "<div class='test-item info'>
    <h3>2. PHP PDO Extension Check</h3>";

if (extension_loaded('pdo_mysql')) {
    echo "<p class='success'>✅ PDO MySQL extension is available</p>";
} else {
    echo "<p class='error'>❌ PDO MySQL extension is not available</p>";
    echo "<p>You need to enable PDO MySQL in your php.ini file.</p>";
}

echo "</div>";

// Test 3: Try to connect to database
echo "<div class='test-item info'>
    <h3>3. Database Connection Test</h3>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p class='success'>✅ Database connection successful!</p>";
        
        // Test 4: Check if database exists
        echo "<div class='test-item info'>
            <h3>4. Database Existence Check</h3>";
        
        try {
            $stmt = $conn->query("SELECT DATABASE() as current_db");
            $result = $stmt->fetch();
            echo "<p class='success'>✅ Connected to database: " . $result['current_db'] . "</p>";
            
            // Test 5: Check if tables exist
            echo "<div class='test-item info'>
                <h3>5. Database Tables Check</h3>";
            
            $required_tables = ['users', 'room_bookings', 'dining_reservations', 'event_reservations'];
            $missing_tables = [];
            
            foreach ($required_tables as $table) {
                try {
                    $stmt = $conn->query("SELECT 1 FROM $table LIMIT 1");
                    echo "<p class='success'>✅ Table '$table' exists</p>";
                } catch (PDOException $e) {
                    $missing_tables[] = $table;
                    echo "<p class='error'>❌ Table '$table' is missing</p>";
                }
            }
            
            if (empty($missing_tables)) {
                echo "<p class='success'>✅ All required tables exist!</p>";
            } else {
                echo "<p class='warning'>⚠️ Missing tables: " . implode(', ', $missing_tables) . "</p>";
                echo "<p>You need to import the database.sql file.</p>";
            }
            
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Error checking database: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Connection error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test 6: Test registration process
echo "<div class='test-item info'>
    <h3>6. Registration Process Test</h3>";

try {
    $db = getDB();
    
    // Test inserting a user
    $test_email = 'test_' . time() . '@example.com';
    $password_hash = password_hash('test123', PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (firstname, lastname, email, phone, country, password_hash) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        'Test', 'User', $test_email, '+1234567890', 'US', $password_hash
    ]);
    
    if ($result) {
        echo "<p class='success'>✅ User registration test successful!</p>";
        
        // Clean up test user
        $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$test_email]);
        echo "<p class='info'>🧹 Test user cleaned up</p>";
    } else {
        echo "<p class='error'>❌ User registration test failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Registration test error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Common solutions
echo "<div class='test-item warning'>
    <h3>🔧 Common Solutions</h3>
    
    <h4>If you get 'Connection failed':</h4>
    <ol>
        <li>Make sure XAMPP/WAMP is running</li>
        <li>Check if MySQL service is started</li>
        <li>Verify database name is 'hotel_dulux'</li>
        <li>Check username/password in config/database.php</li>
    </ol>
    
    <h4>If you get 'Table doesn't exist':</h4>
    <ol>
        <li>Import the database.sql file in phpMyAdmin</li>
        <li>Or run the SQL commands manually</li>
    </ol>
    
    <h4>If you get 'PDO extension not available':</h4>
    <ol>
        <li>Enable PDO in php.ini</li>
        <li>Restart Apache</li>
    </ol>
</div>";

// Next steps
echo "<div class='test-item info'>
    <h3>📋 Next Steps</h3>
    <p>If all tests pass, your database is ready!</p>
    <ol>
        <li><a href='index.php' class='btn'>Visit Homepage</a></li>
        <li><a href='register.php' class='btn'>Test Registration</a></li>
        <li><a href='login.php' class='btn'>Test Login</a></li>
    </ol>
</div>";

echo "</div></body></html>";
?> 
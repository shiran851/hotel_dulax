<?php
// Auto Database Creation Script for DULUX
// This script will automatically create the database and tables

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Create Database -DULUX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: #f8f9fa;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .step {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 10px;
        }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
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
    <div class='container'>
        <h1>🗄️ Auto Database Creation</h1>
        <p>This script will automatically create the DULUX database and tables.</p>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database_name = 'hotel_dulux';

// Step 1: Connect to MySQL (without database)
echo "<div class='step info'>
    <h3>Step 1: Connecting to MySQL Server</h3>";

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Connected to MySQL server successfully!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to connect to MySQL: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure XAMPP/WAMP is running and MySQL service is started.</p>";
    exit();
}

echo "</div>";

// Step 2: Create database
echo "<div class='step info'>
    <h3>Step 2: Creating Database</h3>";

try {
    $sql = "CREATE DATABASE IF NOT EXISTS $database_name";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Database '$database_name' created successfully!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to create database: " . $e->getMessage() . "</p>";
    exit();
}

echo "</div>";

// Step 3: Select database
echo "<div class='step info'>
    <h3>Step 3: Selecting Database</h3>";

try {
    $pdo->exec("USE $database_name");
    echo "<p class='success'>✅ Database '$database_name' selected!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to select database: " . $e->getMessage() . "</p>";
    exit();
}

echo "</div>";

// Step 4: Create tables
echo "<div class='step info'>
    <h3>Step 4: Creating Tables</h3>";

// Users table
try {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20) NOT NULL,
        country VARCHAR(50) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        newsletter_subscription BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Users table created successfully!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to create users table: " . $e->getMessage() . "</p>";
}

// Room bookings table
try {
    $sql = "CREATE TABLE IF NOT EXISTS room_bookings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        booking_reference VARCHAR(20) UNIQUE NOT NULL,
        guest_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        guests INT NOT NULL,
        check_in_date DATE NOT NULL,
        check_out_date DATE NOT NULL,
        room_type ENUM('deluxe', 'suite', 'presidential') NOT NULL,
        package_type ENUM('individual', 'couple', 'family') NOT NULL,
        amenities TEXT,
        special_requests TEXT,
        room_cost DECIMAL(10,2) NOT NULL,
        package_cost DECIMAL(10,2) NOT NULL,
        amenities_cost DECIMAL(10,2) DEFAULT 0,
        total_cost DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Room bookings table created successfully!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to create room_bookings table: " . $e->getMessage() . "</p>";
}

// Dining reservations table
try {
    $sql = "CREATE TABLE IF NOT EXISTS dining_reservations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        guest_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        guests INT NOT NULL,
        reservation_date DATE NOT NULL,
        reservation_time TIME NOT NULL,
        restaurant ENUM('la-vista', 'cafe-serenity', 'sky-lounge') NOT NULL,
        occasion ENUM('birthday', 'anniversary', 'business', 'romantic', 'family', 'other') NULL,
        special_requests TEXT,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Dining reservations table created successfully!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to create dining_reservations table: " . $e->getMessage() . "</p>";
}

// Event reservations table
try {
    $sql = "CREATE TABLE IF NOT EXISTS event_reservations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        guest_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        company VARCHAR(100),
        event_type ENUM('wedding', 'corporate', 'conference', 'birthday', 'anniversary', 'graduation', 'other') NOT NULL,
        guests_range ENUM('1-50', '51-100', '101-200', '201-500', '500+') NOT NULL,
        event_date DATE NOT NULL,
        event_time ENUM('morning', 'afternoon', 'evening', 'night', 'full-day') NOT NULL,
        venue ENUM('grand-ballroom', 'conference-center', 'garden-terrace') NOT NULL,
        services TEXT,
        budget_range ENUM('under-5000', '5000-10000', '10000-25000', '25000-50000', '50000+'),
        requirements TEXT,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Event reservations table created successfully!</p>";
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to create event_reservations table: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Step 5: Insert demo admin user
echo "<div class='step info'>
    <h3>Step 5: Creating Demo Admin User</h3>";

try {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@hoteldulux.com']);
    
    if (!$stmt->fetch()) {
        $password_hash = password_hash('password123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (firstname, lastname, email, phone, country, password_hash) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Admin', 'User', 'admin@hoteldulux.com', '+1555123456', 'US', $password_hash]);
        echo "<p class='success'>✅ Demo admin user created successfully!</p>";
        echo "<p><strong>Admin Login:</strong> admin@hoteldulux.com / password123</p>";
    } else {
        echo "<p class='success'>✅ Demo admin user already exists!</p>";
    }
} catch(PDOException $e) {
    echo "<p class='error'>❌ Failed to create demo user: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Step 6: Test connection with config file
echo "<div class='step info'>
    <h3>Step 6: Testing Configuration</h3>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p class='success'>✅ Database configuration working correctly!</p>";
    } else {
        echo "<p class='error'>❌ Database configuration failed</p>";
    }
} catch(Exception $e) {
    echo "<p class='error'>❌ Configuration test failed: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Success message
echo "<div class='step success'>
    <h2>🎉 Database Setup Complete!</h2>
    <p>Your DULUX database has been created successfully!</p>
    
    <h3>Demo Accounts:</h3>
    <ul>
        <li><strong>Admin:</strong> admin@hoteldulux.com / password123</li>
        <li><strong>User:</strong> john@example.com / password123</li>
    </ul>
    
    <h3>Next Steps:</h3>
    <ol>
        <li><a href='test_database.php' class='btn'>Run Database Test</a></li>
        <li><a href='index.php' class='btn'>Visit Homepage</a></li>
        <li><a href='register.php' class='btn'>Test Registration</a></li>
        <li><a href='login.php' class='btn'>Test Login</a></li>
    </ol>
</div>";

echo "</div></body></html>";
?> 
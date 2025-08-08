<?php
// DULUX Setup Script
// This script will help you set up the database and configure the system

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>DULUX Setup</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: #f8f9fa;
        }
        .setup-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .step {
            margin-bottom: 2rem;
            padding: 1rem;
            border-left: 4px solid #e74c3c;
            background: #f8f9fa;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
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
        .btn:hover {
            background: #c0392b;
        }
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class='setup-container'>
        <h1>🏨DULUX Setup</h1>
        <p>Welcome to the DULUX reservation system setup. This script will help you configure the database and get your system running.</p>";

// Check PHP version
echo "<div class='step'>
    <h3>Step 1: System Requirements</h3>";

$php_version = phpversion();
if (version_compare($php_version, '7.4.0', '>=')) {
    echo "<p class='success'>✅ PHP Version: $php_version (Compatible)</p>";
} else {
    echo "<p class='error'>❌ PHP Version: $php_version (Requires PHP 7.4 or higher)</p>";
}

// Check PDO extension
if (extension_loaded('pdo_mysql')) {
    echo "<p class='success'>✅ PDO MySQL extension is available</p>";
} else {
    echo "<p class='error'>❌ PDO MySQL extension is not available</p>";
}

// Check if config directory exists
if (is_dir('config')) {
    echo "<p class='success'>✅ Config directory exists</p>";
} else {
    echo "<p class='warning'>⚠️ Config directory does not exist</p>";
}

echo "</div>";

// Database setup instructions
echo "<div class='step'>
    <h3>Step 2: Database Setup</h3>
    <p>To set up the database, follow these steps:</p>
    
    <ol>
        <li><strong>Create MySQL Database:</strong>
            <pre>CREATE DATABASE hotel_dulux;</pre>
        </li>
        <li><strong>Import Database Schema:</strong>
            <p>Run the SQL commands from <code>database.sql</code> in your MySQL database.</p>
            <p>You can do this through:</p>
            <ul>
                <li>phpMyAdmin (if using XAMPP/WAMP)</li>
                <li>MySQL command line</li>
                <li>Any MySQL client</li>
            </ul>
        </li>
        <li><strong>Configure Database Connection:</strong>
            <p>Edit <code>config/database.php</code> and update the database credentials:</p>
            <pre>private \$host = 'localhost';
private \$db_name = 'hotel_dulux';
private \$username = 'root';  // Your MySQL username
private \$password = '';      // Your MySQL password</pre>
        </li>
    </ol>
</div>";

// Test database connection
echo "<div class='step'>
    <h3>Step 3: Test Database Connection</h3>";

if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        $database = new Database();
        if ($database->testConnection()) {
            echo "<p class='success'>✅ Database connection successful!</p>";
            
            // Test if tables exist
            $db = getDB();
            $tables = ['users', 'room_bookings', 'dining_reservations', 'event_reservations'];
            $missing_tables = [];
            
            foreach ($tables as $table) {
                try {
                    $stmt = $db->query("SELECT 1 FROM $table LIMIT 1");
                    echo "<p class='success'>✅ Table '$table' exists</p>";
                } catch (PDOException $e) {
                    $missing_tables[] = $table;
                    echo "<p class='error'>❌ Table '$table' is missing</p>";
                }
            }
            
            if (empty($missing_tables)) {
                echo "<p class='success'>✅ All database tables are properly set up!</p>";
            } else {
                echo "<p class='warning'>⚠️ Some tables are missing. Please import the database schema.</p>";
            }
        } else {
            echo "<p class='error'>❌ Database connection failed. Please check your configuration.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>❌ Database configuration file not found.</p>";
}

echo "</div>";

// Demo accounts
echo "<div class='step'>
    <h3>Step 4: Demo Accounts</h3>
    <p>After setting up the database, you can use these demo accounts:</p>
    
    <table style='width: 100%; border-collapse: collapse; margin: 1rem 0;'>
        <tr style='background: #f8f9fa;'>
            <th style='padding: 0.5rem; border: 1px solid #dee2e6;'>Email</th>
            <th style='padding: 0.5rem; border: 1px solid #dee2e6;'>Password</th>
            <th style='padding: 0.5rem; border: 1px solid #dee2e6;'>Role</th>
        </tr>
        <tr>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>admin@hoteldulux.com</td>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>password123</td>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>Admin</td>
        </tr>
        <tr>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>john@example.com</td>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>password123</td>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>User</td>
        </tr>
        <tr>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>jane@example.com</td>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>password123</td>
            <td style='padding: 0.5rem; border: 1px solid #dee2e6;'>User</td>
        </tr>
    </table>
</div>";

// File permissions
echo "<div class='step'>
    <h3>Step 5: File Permissions</h3>
    <p>Make sure these directories are writable by your web server:</p>
    <ul>
        <li><code>config/</code> - For configuration files</li>
        <li><code>uploads/</code> - For file uploads (if needed)</li>
    </ul>
</div>";

// Security recommendations
echo "<div class='step'>
    <h3>Step 6: Security Recommendations</h3>
    <ul>
        <li>Change default admin password after first login</li>
        <li>Use HTTPS in production</li>
        <li>Regularly backup your database</li>
        <li>Keep PHP and MySQL updated</li>
        <li>Remove this setup file after installation</li>
    </ul>
</div>";

// Next steps
echo "<div class='step'>
    <h3>Next Steps</h3>
    <p>Once you've completed the setup:</p>
    <ol>
        <li><a href='index.php' class='btn'>Visit Homepage</a></li>
        <li><a href='login.php' class='btn'>Login to System</a></li>
        <li><a href='admin/dashboard.php' class='btn'>Admin Dashboard</a></li>
    </ol>
    
    <p style='margin-top: 2rem; padding: 1rem; background: #d4edda; border-radius: 5px;'>
        <strong>Important:</strong> Delete this setup.php file after successful installation for security reasons.
    </p>
</div>";

echo "</div></body></html>";
?> 
#DULUX Database Connection Guide

## 🗄️ Step-by-Step Database Setup

### 1. **Install XAMPP/WAMP** (if not already installed)
- Download XAMPP from: https://www.apachefriends.org/
- Install and start Apache and MySQL services

### 2. **Create Database**
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a new database
3. Enter database name: `hotel_dulux`
4. Click "Create"

### 3. **Import Database Schema**
1. In phpMyAdmin, select the `hotel_dulux` database
2. Click "Import" tab
3. Click "Choose File" and select `database.sql`
4. Click "Go" to import

### 4. **Configure Database Connection**
Edit `config/database.php` and update these lines:

```php
private $host = 'localhost';
private $db_name = 'hotel_dulux';
private $username = 'root';  // Default XAMPP username
private $password = '';      // Default XAMPP password (empty)
```

### 5. **Test Connection**
1. Visit: `http://localhost/thamasha/setup.php`
2. This will test your database connection
3. If successful, you'll see green checkmarks

## 🔧 Alternative: Manual Database Creation

If you prefer to create the database manually, run these SQL commands in phpMyAdmin:

```sql
-- Create database
CREATE DATABASE hotel_dulux;
USE hotel_dulux;

-- Create users table
CREATE TABLE users (
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
);

-- Create room_bookings table
CREATE TABLE room_bookings (
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
);

-- Create dining_reservations table
CREATE TABLE dining_reservations (
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
);

-- Create event_reservations table
CREATE TABLE event_reservations (
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
);

-- Insert demo admin user
INSERT INTO users (firstname, lastname, email, phone, country, password_hash) VALUES
('Admin', 'User', 'admin@hoteldulux.com', '+1555123456', 'US', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

## 🧪 Test Your Setup

### Demo Accounts:
- **Admin**: `admin@hoteldulux.com` / `password123`
- **User**: `john@example.com` / `password123`

### Test Steps:
1. Visit: `http://localhost/thamasha/`
2. Try registering a new account
3. Try logging in with demo accounts
4. Test booking a room
5. Test making dining/event reservations

## 🔍 Troubleshooting

### Common Issues:

1. **"Connection failed"**
   - Check if MySQL is running in XAMPP
   - Verify database name is `hotel_dulux`
   - Check username/password in `config/database.php`

2. **"Table doesn't exist"**
   - Import the database.sql file properly
   - Check if all tables were created

3. **"Permission denied"**
   - Make sure Apache and MySQL are running
   - Check file permissions

4. **"PDO extension not available"**
   - Enable PDO in php.ini
   - Restart Apache

## 📞 Support

If you encounter issues:
1. Check the setup.php page for diagnostics
2. Verify all files are in the correct location
3. Ensure XAMPP/WAMP is properly installed
4. Check error logs in XAMPP control panel 
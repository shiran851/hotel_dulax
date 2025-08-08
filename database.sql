--DULUX Database Schema
CREATE DATABASE IF NOT EXISTS hotel_dulux;
USE hotel_dulux;

-- Users table
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

-- Room bookings table
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

-- Dining reservations table
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

-- Event reservations table
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
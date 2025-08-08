--DULUX Database Schema
-- Created for the luxury hotel reservation system

-- Create database
CREATE DATABASE IF NOT EXISTS hotel_dulux;
USE hotel_dulux;

-- Users table for login/register system
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

-- Contact messages table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Settings table for system configuration
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO admin_users (username, email, password_hash, role) VALUES 
('admin', 'admin@hoteldulux.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('hotel_name', 'Hotel DULUX', 'Hotel name'),
('hotel_email', 'info@hoteldulux.com', 'Hotel contact email'),
('hotel_phone', '+1 (555) 123-4567', 'Hotel contact phone'),
('hotel_address', '123 Luxury Street, City Center', 'Hotel address'),
('room_prices', '{"deluxe": 150, "suite": 250, "presidential": 500}', 'Room prices per night'),
('package_prices', '{"individual": 120, "couple": 200, "family": 300}', 'Package prices per night'),
('amenity_prices', '{"pool": 20, "spa": 80, "transfer": 50, "room-service": 15}', 'Amenity prices');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_room_bookings_reference ON room_bookings(booking_reference);
CREATE INDEX idx_room_bookings_dates ON room_bookings(check_in_date, check_out_date);
CREATE INDEX idx_dining_reservations_date ON dining_reservations(reservation_date);
CREATE INDEX idx_event_reservations_date ON event_reservations(event_date);
CREATE INDEX idx_contact_messages_status ON contact_messages(status);

-- Create views for common queries
CREATE VIEW active_bookings AS
SELECT 
    rb.*,
    CONCAT(u.firstname, ' ', u.lastname) as user_full_name,
    u.email as user_email
FROM room_bookings rb
LEFT JOIN users u ON rb.user_id = u.id
WHERE rb.status IN ('pending', 'confirmed');

CREATE VIEW active_dining_reservations AS
SELECT 
    dr.*,
    CONCAT(u.firstname, ' ', u.lastname) as user_full_name,
    u.email as user_email
FROM dining_reservations dr
LEFT JOIN users u ON dr.user_id = u.id
WHERE dr.status IN ('pending', 'confirmed');

CREATE VIEW active_event_reservations AS
SELECT 
    er.*,
    CONCAT(u.firstname, ' ', u.lastname) as user_full_name,
    u.email as user_email
FROM event_reservations er
LEFT JOIN users u ON er.user_id = u.id
WHERE er.status IN ('pending', 'confirmed');

-- Create stored procedures for common operations
DELIMITER //

CREATE PROCEDURE GetUserBookings(IN user_email VARCHAR(100))
BEGIN
    SELECT 
        rb.*,
        CASE 
            WHEN rb.status = 'pending' THEN 'Pending'
            WHEN rb.status = 'confirmed' THEN 'Confirmed'
            WHEN rb.status = 'cancelled' THEN 'Cancelled'
            WHEN rb.status = 'completed' THEN 'Completed'
        END as status_text
    FROM room_bookings rb
    WHERE rb.email = user_email
    ORDER BY rb.created_at DESC;
END //

CREATE PROCEDURE GetUserDiningReservations(IN user_email VARCHAR(100))
BEGIN
    SELECT 
        dr.*,
        CASE 
            WHEN dr.status = 'pending' THEN 'Pending'
            WHEN dr.status = 'confirmed' THEN 'Confirmed'
            WHEN dr.status = 'cancelled' THEN 'Cancelled'
            WHEN dr.status = 'completed' THEN 'Completed'
        END as status_text
    FROM dining_reservations dr
    WHERE dr.email = user_email
    ORDER BY dr.created_at DESC;
END //

CREATE PROCEDURE GetUserEventReservations(IN user_email VARCHAR(100))
BEGIN
    SELECT 
        er.*,
        CASE 
            WHEN er.status = 'pending' THEN 'Pending'
            WHEN er.status = 'confirmed' THEN 'Confirmed'
            WHEN er.status = 'cancelled' THEN 'Cancelled'
            WHEN er.status = 'completed' THEN 'Completed'
        END as status_text
    FROM event_reservations er
    WHERE er.email = user_email
    ORDER BY er.created_at DESC;
END //

CREATE PROCEDURE UpdateBookingStatus(IN booking_id INT, IN new_status VARCHAR(20))
BEGIN
    UPDATE room_bookings 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = booking_id;
END //

CREATE PROCEDURE UpdateDiningStatus(IN reservation_id INT, IN new_status VARCHAR(20))
BEGIN
    UPDATE dining_reservations 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = reservation_id;
END //

CREATE PROCEDURE UpdateEventStatus(IN reservation_id INT, IN new_status VARCHAR(20))
BEGIN
    UPDATE event_reservations 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = reservation_id;
END //

DELIMITER ;

-- Insert sample data for testing
INSERT INTO users (firstname, lastname, email, phone, country, password_hash) VALUES
('John', 'Doe', 'john@example.com', '+1234567890', 'US', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane', 'Smith', 'jane@example.com', '+1987654321', 'CA', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Admin', 'User', 'admin@hoteldulux.com', '+1555123456', 'US', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample bookings
INSERT INTO room_bookings (user_id, booking_reference, guest_name, email, phone, guests, check_in_date, check_out_date, room_type, package_type, room_cost, package_cost, total_cost) VALUES
(1, 'DULUX20241201001', 'John Doe', 'john@example.com', '+1234567890', 2, '2024-12-15', '2024-12-17', 'deluxe', 'couple', 300.00, 400.00, 700.00),
(2, 'DULUX20241201002', 'Jane Smith', 'jane@example.com', '+1987654321', 1, '2024-12-20', '2024-12-22', 'suite', 'individual', 500.00, 240.00, 740.00);

-- Insert sample dining reservations
INSERT INTO dining_reservations (user_id, guest_name, email, phone, guests, reservation_date, reservation_time, restaurant, occasion) VALUES
(1, 'John Doe', 'john@example.com', '+1234567890', 2, '2024-12-16', '19:00:00', 'la-vista', 'romantic'),
(2, 'Jane Smith', 'jane@example.com', '+1987654321', 4, '2024-12-21', '18:30:00', 'cafe-serenity', 'family');

-- Insert sample event reservations
INSERT INTO event_reservations (user_id, guest_name, email, phone, event_type, guests_range, event_date, event_time, venue, budget_range) VALUES
(1, 'John Doe', 'john@example.com', '+1234567890', 'wedding', '101-200', '2024-12-25', 'full-day', 'grand-ballroom', '25000-50000'),
(2, 'Jane Smith', 'jane@example.com', '+1987654321', 'corporate', '51-100', '2024-12-30', 'full-day', 'conference-center', '10000-25000'); 
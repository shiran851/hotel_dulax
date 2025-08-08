-- Additional tables for DULUX Hotel Management System

-- Activity logs table for tracking user actions
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Dining reports table for completed reservations
CREATE TABLE dining_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_id INT,
    guest_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    restaurant ENUM('la-vista', 'cafe-serenity', 'sky-lounge') NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests INT NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES dining_reservations(id) ON DELETE SET NULL
);

-- Pricing tables for dynamic pricing management
CREATE TABLE room_pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_type ENUM('deluxe', 'suite', 'presidential') NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    weekend_multiplier DECIMAL(3,2) DEFAULT 1.2,
    peak_season_multiplier DECIMAL(3,2) DEFAULT 1.5,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE package_pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_type ENUM('individual', 'couple', 'family') NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE dining_pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    restaurant ENUM('la-vista', 'cafe-serenity', 'sky-lounge') NOT NULL,
    price_per_guest DECIMAL(10,2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add price column to dining_reservations if not exists
ALTER TABLE dining_reservations ADD COLUMN price DECIMAL(10,2) DEFAULT 0;

-- Insert default pricing data
INSERT INTO room_pricing (room_type, base_price) VALUES
('deluxe', 150.00),
('suite', 250.00),
('presidential', 500.00);

INSERT INTO package_pricing (package_type, base_price) VALUES
('individual', 120.00),
('couple', 200.00),
('family', 300.00);

INSERT INTO dining_pricing (restaurant, price_per_guest) VALUES
('la-vista', 50.00),
('cafe-serenity', 40.00),
('sky-lounge', 60.00);
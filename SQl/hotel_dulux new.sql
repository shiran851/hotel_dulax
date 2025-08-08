-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2025 at 10:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_dulux`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserBookings` (IN `user_email` VARCHAR(100))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserDiningReservations` (IN `user_email` VARCHAR(100))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserEventReservations` (IN `user_email` VARCHAR(100))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateBookingStatus` (IN `booking_id` INT, IN `new_status` VARCHAR(20))   BEGIN
    UPDATE room_bookings 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = booking_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateDiningStatus` (IN `reservation_id` INT, IN `new_status` VARCHAR(20))   BEGIN
    UPDATE dining_reservations 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = reservation_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEventStatus` (IN `reservation_id` INT, IN `new_status` VARCHAR(20))   BEGIN
    UPDATE event_reservations 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = reservation_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_bookings`
-- (See below for the actual view)
--
CREATE TABLE `active_bookings` (
`id` int(11)
,`user_id` int(11)
,`booking_reference` varchar(20)
,`guest_name` varchar(100)
,`email` varchar(100)
,`phone` varchar(20)
,`guests` int(11)
,`check_in_date` date
,`check_out_date` date
,`room_type` enum('deluxe','suite','presidential')
,`package_type` enum('individual','couple','family')
,`amenities` text
,`special_requests` text
,`room_cost` decimal(10,2)
,`package_cost` decimal(10,2)
,`amenities_cost` decimal(10,2)
,`total_cost` decimal(10,2)
,`status` enum('pending','confirmed','cancelled','completed')
,`created_at` timestamp
,`updated_at` timestamp
,`user_full_name` varchar(101)
,`user_email` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_dining_reservations`
-- (See below for the actual view)
--
CREATE TABLE `active_dining_reservations` (
`id` int(11)
,`user_id` int(11)
,`guest_name` varchar(100)
,`email` varchar(100)
,`phone` varchar(20)
,`guests` int(11)
,`reservation_date` date
,`reservation_time` time
,`restaurant` enum('la-vista','cafe-serenity','sky-lounge')
,`occasion` enum('birthday','anniversary','business','romantic','family','other')
,`special_requests` text
,`status` enum('pending','confirmed','cancelled','completed')
,`created_at` timestamp
,`updated_at` timestamp
,`user_full_name` varchar(101)
,`user_email` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_event_reservations`
-- (See below for the actual view)
--
CREATE TABLE `active_event_reservations` (
`id` int(11)
,`user_id` int(11)
,`guest_name` varchar(100)
,`email` varchar(100)
,`phone` varchar(20)
,`company` varchar(100)
,`event_type` enum('wedding','corporate','conference','birthday','anniversary','graduation','other')
,`guests_range` enum('1-50','51-100','101-200','201-500','500+')
,`event_date` date
,`event_time` enum('morning','afternoon','evening','night','full-day')
,`venue` enum('grand-ballroom','conference-center','garden-terrace')
,`services` text
,`budget_range` enum('under-5000','5000-10000','10000-25000','25000-50000','50000+')
,`requirements` text
,`status` enum('pending','confirmed','cancelled','completed')
,`created_at` timestamp
,`updated_at` timestamp
,`user_full_name` varchar(101)
,`user_email` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 3, 'login', 'User logged in successfully', '2025-07-31 17:56:08'),
(2, 3, 'login', 'User logged in successfully', '2025-07-31 17:57:53'),
(3, 3, 'logout', 'User logged out', '2025-07-31 17:59:07'),
(4, 3, 'login', 'User logged in successfully', '2025-07-31 18:12:35'),
(5, 3, 'logout', 'User logged out', '2025-07-31 18:22:03'),
(6, NULL, 'login', 'User logged in successfully', '2025-07-31 18:59:31'),
(7, 3, 'login', 'User logged in successfully', '2025-07-31 21:16:40'),
(8, 3, 'logout', 'User logged out', '2025-07-31 21:21:42'),
(9, NULL, 'login', 'User logged in successfully', '2025-07-31 21:21:59'),
(10, 3, 'login', 'User logged in successfully', '2025-07-31 21:26:18'),
(11, 3, 'logout', 'User logged out', '2025-08-01 04:28:03'),
(12, NULL, 'login', 'User logged in successfully', '2025-08-01 04:28:39'),
(13, NULL, 'logout', 'User logged out', '2025-08-01 04:47:33'),
(14, 10, 'register', 'New user registered', '2025-08-01 04:49:15'),
(15, 10, 'login', 'User logged in successfully', '2025-08-01 04:50:03'),
(16, 10, 'event_reservation', 'Made event reservation for birthday at garden-terrace for 51-100 guests', '2025-08-01 04:51:57'),
(17, 10, 'logout', 'User logged out', '2025-08-01 04:52:15'),
(18, 3, 'login', 'User logged in successfully', '2025-08-01 04:52:31'),
(19, 3, 'logout', 'User logged out', '2025-08-01 04:53:02'),
(20, 10, 'login', 'User logged in successfully', '2025-08-01 04:53:24'),
(21, 10, 'logout', 'User logged out', '2025-08-01 05:01:36'),
(22, 3, 'login', 'User logged in successfully', '2025-08-01 05:01:58'),
(23, 3, 'logout', 'User logged out', '2025-08-01 09:12:09'),
(24, 3, 'login', 'User logged in successfully', '2025-08-01 09:12:27'),
(25, 3, 'logout', 'User logged out', '2025-08-01 09:12:35'),
(26, NULL, 'register', 'New user registered', '2025-08-01 09:13:38'),
(27, NULL, 'login', 'User logged in successfully', '2025-08-01 09:13:52'),
(28, NULL, 'logout', 'User logged out', '2025-08-01 09:37:59'),
(29, NULL, 'login', 'User logged in successfully', '2025-08-01 09:42:53'),
(30, 3, 'login', 'User logged in successfully', '2025-08-08 05:18:30'),
(31, 3, 'dining_reservation', 'Reserved at sky-lounge for 5 guests on 2025-08-16', '2025-08-08 06:33:11'),
(32, 3, 'event_reservation', 'Made event reservation for birthday at garden-terrace for 51-100 guests', '2025-08-08 06:36:54'),
(33, 3, 'event_reservation', 'Made event reservation for anniversary at conference-center for 101-200 guests', '2025-08-08 06:42:09'),
(34, 3, 'dining_reservation', 'Reserved at sky-lounge for 5 guests on 2025-09-08', '2025-08-08 06:44:10'),
(35, 3, 'logout', 'User logged out', '2025-08-08 07:24:00'),
(36, 3, 'login', 'User logged in successfully', '2025-08-08 07:32:53'),
(37, 3, 'logout', 'User logged out', '2025-08-08 07:49:47'),
(38, NULL, 'register', 'New user registered', '2025-08-08 07:50:55'),
(39, NULL, 'login', 'User logged in successfully', '2025-08-08 07:51:47'),
(44, 10, 'login', 'User logged in successfully', '2025-08-08 08:10:12'),
(45, 10, 'dining_reservation', 'Reserved at la-vista for 2 guests on 2025-08-27', '2025-08-08 08:20:13'),
(46, 10, 'dining_reservation', 'Reserved at la-vista for 2 guests on 2025-08-27', '2025-08-08 08:22:42'),
(47, 10, 'event_reservation', 'Made event reservation for wedding at grand-ballroom for 201-500 guests', '2025-08-08 08:24:11');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@hoteldulux.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2025-07-31 17:08:53', '2025-07-31 17:08:53');

-- --------------------------------------------------------

--
-- Table structure for table `amenity_prices`
--

CREATE TABLE `amenity_prices` (
  `id` int(11) NOT NULL,
  `amenity` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenity_prices`
--

INSERT INTO `amenity_prices` (`id`, `amenity`, `price`) VALUES
(1, 'pool', 1000.00),
(2, 'spa', 2500.00),
(3, 'transfer', 0.00),
(4, 'room-service', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_reports`
--

CREATE TABLE `booking_reports` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `guests` int(11) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `booking_reference` varchar(50) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dining_pricing`
--

CREATE TABLE `dining_pricing` (
  `id` int(11) NOT NULL,
  `restaurant` enum('la-vista','cafe-serenity','sky-lounge') NOT NULL,
  `price_per_guest` decimal(10,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dining_pricing`
--

INSERT INTO `dining_pricing` (`id`, `restaurant`, `price_per_guest`, `updated_at`) VALUES
(1, 'la-vista', 50.00, '2025-08-08 06:14:04'),
(2, 'cafe-serenity', 40.00, '2025-08-08 06:14:04'),
(3, 'sky-lounge', 60.00, '2025-08-08 06:14:04');

-- --------------------------------------------------------

--
-- Table structure for table `dining_reports`
--

CREATE TABLE `dining_reports` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `guests` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `reservation_time` time DEFAULT NULL,
  `restaurant` varchar(50) DEFAULT NULL,
  `occasion` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dining_reservations`
--

CREATE TABLE `dining_reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `guests` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `restaurant` enum('la-vista','cafe-serenity','sky-lounge') NOT NULL,
  `occasion` enum('birthday','anniversary','business','romantic','family','other') DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dining_reservations`
--

INSERT INTO `dining_reservations` (`id`, `user_id`, `guest_name`, `email`, `phone`, `guests`, `reservation_date`, `reservation_time`, `restaurant`, `occasion`, `special_requests`, `status`, `created_at`, `updated_at`, `price`) VALUES
(2, 2, 'Jane Smith', 'jane@example.com', '+1987654321', 4, '2024-12-21', '18:30:00', 'cafe-serenity', 'family', NULL, 'completed', '2025-07-31 17:08:54', '2025-08-08 06:21:35', 0.00),
(3, 3, 'ishan puila', 'ishan@gmail.com', '0711234567', 5, '2025-08-16', '18:00:00', 'sky-lounge', 'romantic', '', 'completed', '2025-08-08 06:33:11', '2025-08-08 06:33:43', 300.00),
(4, 3, 'ishan puila', 'ishan@gmail.com', '0711234678', 5, '2025-09-08', '13:13:00', 'sky-lounge', 'business', '', 'completed', '2025-08-08 06:44:10', '2025-08-08 06:44:32', 300.00),
(6, 10, 'ishan puila', 'ishan@gmail.com', '0714346630', 2, '2025-08-27', '20:00:00', 'la-vista', 'romantic', '', 'completed', '2025-08-08 08:22:42', '2025-08-08 08:23:09', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `event_reports`
--

CREATE TABLE `event_reports` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `venue` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_reservations`
--

CREATE TABLE `event_reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `event_type` enum('wedding','corporate','conference','birthday','anniversary','graduation','other') NOT NULL,
  `guests_range` enum('1-50','51-100','101-200','201-500','500+') NOT NULL,
  `event_date` date NOT NULL,
  `event_time` enum('morning','afternoon','evening','night','full-day') NOT NULL,
  `venue` enum('grand-ballroom','conference-center','garden-terrace') NOT NULL,
  `services` text DEFAULT NULL,
  `budget_range` enum('under-5000','5000-10000','10000-25000','25000-50000','50000+') DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_reservations`
--

INSERT INTO `event_reservations` (`id`, `user_id`, `guest_name`, `email`, `phone`, `company`, `event_type`, `guests_range`, `event_date`, `event_time`, `venue`, `services`, `budget_range`, `requirements`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'John Doe', 'john@example.com', '+1234567890', NULL, 'wedding', '101-200', '2024-12-25', 'full-day', 'grand-ballroom', NULL, '25000-50000', NULL, 'completed', '2025-07-31 17:08:54', '2025-08-08 05:30:21'),
(2, 2, 'Jane Smith', 'jane@example.com', '+1987654321', NULL, 'corporate', '51-100', '2024-12-30', 'full-day', 'conference-center', NULL, '10000-25000', NULL, 'completed', '2025-07-31 17:08:54', '2025-08-08 05:30:26'),
(3, 10, 'ishan puila', 'ishan@gmail.com', '0723456780', 'ushfjkfxhkjs', 'birthday', '51-100', '2025-08-09', 'evening', 'garden-terrace', 'catering, photography, accommodation', '5000-10000', '', 'completed', '2025-08-01 04:51:57', '2025-08-08 05:30:16'),
(4, 3, 'ishan puila', 'ishan@gmail.com', '0723456780', 'ushfjkfxhkjs', 'birthday', '51-100', '2025-08-30', 'evening', 'garden-terrace', 'catering, decoration, audio-visual', 'under-5000', '', 'completed', '2025-08-08 06:36:54', '2025-08-08 06:37:26'),
(5, 3, 'ishan puila', 'ishan@gmail.com', '0711234567', 'ushfjkfxhkjs', 'anniversary', '101-200', '2025-08-29', 'full-day', 'conference-center', 'catering, decoration', '5000-10000', '', 'completed', '2025-08-08 06:42:09', '2025-08-08 06:42:45'),
(6, 10, 'ishan puila', 'ishan@gmail.com', '0711234678', 'ushfjkfxhkjs', 'wedding', '201-500', '2025-12-31', 'full-day', 'grand-ballroom', 'catering, decoration', '25000-50000', '', 'completed', '2025-08-08 08:24:11', '2025-08-08 08:24:33');

-- --------------------------------------------------------

--
-- Table structure for table `package_prices`
--

CREATE TABLE `package_prices` (
  `id` int(11) NOT NULL,
  `package_type` enum('individual','couple','family') NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_prices`
--

INSERT INTO `package_prices` (`id`, `package_type`, `price`) VALUES
(1, 'individual', 3000.00),
(2, 'couple', 6000.00),
(3, 'family', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `package_pricing`
--

CREATE TABLE `package_pricing` (
  `id` int(11) NOT NULL,
  `package_type` enum('individual','couple','family') NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_pricing`
--

INSERT INTO `package_pricing` (`id`, `package_type`, `base_price`, `updated_at`) VALUES
(1, 'individual', 120.00, '2025-08-08 06:14:04'),
(2, 'couple', 200.00, '2025-08-08 06:14:04'),
(3, 'family', 300.00, '2025-08-08 06:14:04');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_reference` varchar(20) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `guests` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `room_type` enum('deluxe','suite','presidential') NOT NULL,
  `package_type` enum('individual','couple','family') NOT NULL,
  `amenities` text DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `room_cost` decimal(10,2) NOT NULL,
  `package_cost` decimal(10,2) NOT NULL,
  `amenities_cost` decimal(10,2) DEFAULT 0.00,
  `total_cost` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `user_id`, `booking_reference`, `guest_name`, `email`, `phone`, `guests`, `check_in_date`, `check_out_date`, `room_type`, `package_type`, `amenities`, `special_requests`, `room_cost`, `package_cost`, `amenities_cost`, `total_cost`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'DULUX20241201001', 'John Doe', 'john@example.com', '+1234567890', 2, '2024-12-15', '2024-12-17', 'deluxe', 'couple', NULL, NULL, 300.00, 400.00, 0.00, 700.00, 'completed', '2025-07-31 17:08:54', '2025-08-08 06:25:28'),
(2, 2, 'DULUX20241201002', 'Jane Smith', 'jane@example.com', '+1987654321', 1, '2024-12-20', '2024-12-22', 'suite', 'individual', NULL, NULL, 500.00, 240.00, 0.00, 740.00, 'completed', '2025-07-31 17:08:54', '2025-08-08 06:25:30'),
(3, 3, 'BOOK3FCDDF', 'ishan puila', 'ishan@gmail.com', '0723456780', 4, '2025-08-09', '2025-08-10', 'suite', 'family', NULL, '', 250.00, 300.00, 0.00, 550.00, 'completed', '2025-08-08 06:30:49', '2025-08-08 06:31:17'),
(7, 10, 'BOOKA09124', 'ishan puila', 'ishan@gmail.com', '0711234678', 2, '2025-08-18', '2025-08-19', 'presidential', 'couple', NULL, '', 500.00, 200.00, 0.00, 700.00, 'completed', '2025-08-08 08:13:34', '2025-08-08 08:19:17');

-- --------------------------------------------------------

--
-- Table structure for table `room_prices`
--

CREATE TABLE `room_prices` (
  `id` int(11) NOT NULL,
  `room_type` enum('deluxe','suite','presidential') NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_prices`
--

INSERT INTO `room_prices` (`id`, `room_type`, `price`) VALUES
(1, 'deluxe', 150.00),
(2, 'suite', 250.00),
(3, 'presidential', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `room_pricing`
--

CREATE TABLE `room_pricing` (
  `id` int(11) NOT NULL,
  `room_type` enum('deluxe','suite','presidential') NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `weekend_multiplier` decimal(3,2) DEFAULT 1.20,
  `peak_season_multiplier` decimal(3,2) DEFAULT 1.50,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_pricing`
--

INSERT INTO `room_pricing` (`id`, `room_type`, `base_price`, `weekend_multiplier`, `peak_season_multiplier`, `updated_at`) VALUES
(1, 'deluxe', 150.00, 1.20, 1.50, '2025-08-08 06:14:04'),
(2, 'suite', 250.00, 1.20, 1.50, '2025-08-08 06:14:04'),
(3, 'presidential', 500.00, 1.20, 1.50, '2025-08-08 06:14:04');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'hotel_name', 'Hotel DULUX', 'Hotel name', '2025-07-31 17:08:53'),
(2, 'hotel_email', 'info@hoteldulux.com', 'Hotel contact email', '2025-07-31 17:08:53'),
(3, 'hotel_phone', '+1 (555) 123-4567', 'Hotel contact phone', '2025-07-31 17:08:53'),
(4, 'hotel_address', '123 Luxury Street, City Center', 'Hotel address', '2025-07-31 17:08:53'),
(5, 'room_prices', '{\"deluxe\": 150, \"suite\": 250, \"presidential\": 500}', 'Room prices per night', '2025-07-31 17:08:53'),
(6, 'package_prices', '{\"individual\": 120, \"couple\": 200, \"family\": 300}', 'Package prices per night', '2025-07-31 17:08:53'),
(7, 'amenity_prices', '{\"pool\": 20, \"spa\": 80, \"transfer\": 50, \"room-service\": 15}', 'Amenity prices', '2025-07-31 17:08:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `country` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `newsletter_subscription` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `phone`, `country`, `password_hash`, `newsletter_subscription`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Doe', 'john@example.com', '+1234567890', 'US', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-07-31 17:08:54', '2025-07-31 17:08:54'),
(2, 'Jane', 'Smith', 'jane@example.com', '+1987654321', 'CA', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-07-31 17:08:54', '2025-07-31 17:08:54'),
(3, 'Admin', 'User', 'admin@hoteldulux.com', '+1555123456', 'US', '$2y$10$p4EP8wnC/EucfBU0vVUG1ukAe5leRlyLzkURVhlm0Y/4yoG29FXUm', 0, '2025-07-31 17:08:54', '2025-07-31 17:49:42'),
(10, 'ishan', 'puila', 'ishan@gmail.com', '0711234567', 'other', '$2y$10$Q968Zo0mxOq/VM0RA9jQU.FmBTliQp/e/Lprpn3NI5QvLEZr51MQe', 1, '2025-08-01 04:49:15', '2025-08-01 04:49:15');

-- --------------------------------------------------------

--
-- Structure for view `active_bookings`
--
DROP TABLE IF EXISTS `active_bookings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_bookings`  AS SELECT `rb`.`id` AS `id`, `rb`.`user_id` AS `user_id`, `rb`.`booking_reference` AS `booking_reference`, `rb`.`guest_name` AS `guest_name`, `rb`.`email` AS `email`, `rb`.`phone` AS `phone`, `rb`.`guests` AS `guests`, `rb`.`check_in_date` AS `check_in_date`, `rb`.`check_out_date` AS `check_out_date`, `rb`.`room_type` AS `room_type`, `rb`.`package_type` AS `package_type`, `rb`.`amenities` AS `amenities`, `rb`.`special_requests` AS `special_requests`, `rb`.`room_cost` AS `room_cost`, `rb`.`package_cost` AS `package_cost`, `rb`.`amenities_cost` AS `amenities_cost`, `rb`.`total_cost` AS `total_cost`, `rb`.`status` AS `status`, `rb`.`created_at` AS `created_at`, `rb`.`updated_at` AS `updated_at`, concat(`u`.`firstname`,' ',`u`.`lastname`) AS `user_full_name`, `u`.`email` AS `user_email` FROM (`room_bookings` `rb` left join `users` `u` on(`rb`.`user_id` = `u`.`id`)) WHERE `rb`.`status` in ('pending','confirmed') ;

-- --------------------------------------------------------

--
-- Structure for view `active_dining_reservations`
--
DROP TABLE IF EXISTS `active_dining_reservations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_dining_reservations`  AS SELECT `dr`.`id` AS `id`, `dr`.`user_id` AS `user_id`, `dr`.`guest_name` AS `guest_name`, `dr`.`email` AS `email`, `dr`.`phone` AS `phone`, `dr`.`guests` AS `guests`, `dr`.`reservation_date` AS `reservation_date`, `dr`.`reservation_time` AS `reservation_time`, `dr`.`restaurant` AS `restaurant`, `dr`.`occasion` AS `occasion`, `dr`.`special_requests` AS `special_requests`, `dr`.`status` AS `status`, `dr`.`created_at` AS `created_at`, `dr`.`updated_at` AS `updated_at`, concat(`u`.`firstname`,' ',`u`.`lastname`) AS `user_full_name`, `u`.`email` AS `user_email` FROM (`dining_reservations` `dr` left join `users` `u` on(`dr`.`user_id` = `u`.`id`)) WHERE `dr`.`status` in ('pending','confirmed') ;

-- --------------------------------------------------------

--
-- Structure for view `active_event_reservations`
--
DROP TABLE IF EXISTS `active_event_reservations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_event_reservations`  AS SELECT `er`.`id` AS `id`, `er`.`user_id` AS `user_id`, `er`.`guest_name` AS `guest_name`, `er`.`email` AS `email`, `er`.`phone` AS `phone`, `er`.`company` AS `company`, `er`.`event_type` AS `event_type`, `er`.`guests_range` AS `guests_range`, `er`.`event_date` AS `event_date`, `er`.`event_time` AS `event_time`, `er`.`venue` AS `venue`, `er`.`services` AS `services`, `er`.`budget_range` AS `budget_range`, `er`.`requirements` AS `requirements`, `er`.`status` AS `status`, `er`.`created_at` AS `created_at`, `er`.`updated_at` AS `updated_at`, concat(`u`.`firstname`,' ',`u`.`lastname`) AS `user_full_name`, `u`.`email` AS `user_email` FROM (`event_reservations` `er` left join `users` `u` on(`er`.`user_id` = `u`.`id`)) WHERE `er`.`status` in ('pending','confirmed') ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_user` (`user_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `amenity_prices`
--
ALTER TABLE `amenity_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `amenity` (`amenity`);

--
-- Indexes for table `booking_reports`
--
ALTER TABLE `booking_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contact_messages_status` (`status`);

--
-- Indexes for table `dining_pricing`
--
ALTER TABLE `dining_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dining_reports`
--
ALTER TABLE `dining_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dining_reservations`
--
ALTER TABLE `dining_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dining_reservations_date` (`reservation_date`),
  ADD KEY `fk_dining_user` (`user_id`);

--
-- Indexes for table `event_reports`
--
ALTER TABLE `event_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_reservations`
--
ALTER TABLE `event_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_reservations_date` (`event_date`),
  ADD KEY `fk_event_user` (`user_id`);

--
-- Indexes for table `package_prices`
--
ALTER TABLE `package_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_type` (`package_type`);

--
-- Indexes for table `package_pricing`
--
ALTER TABLE `package_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_reference` (`booking_reference`),
  ADD KEY `idx_room_bookings_reference` (`booking_reference`),
  ADD KEY `idx_room_bookings_dates` (`check_in_date`,`check_out_date`),
  ADD KEY `fk_room_user` (`user_id`);

--
-- Indexes for table `room_prices`
--
ALTER TABLE `room_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_type` (`room_type`);

--
-- Indexes for table `room_pricing`
--
ALTER TABLE `room_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `amenity_prices`
--
ALTER TABLE `amenity_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `booking_reports`
--
ALTER TABLE `booking_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dining_pricing`
--
ALTER TABLE `dining_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dining_reports`
--
ALTER TABLE `dining_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dining_reservations`
--
ALTER TABLE `dining_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_reports`
--
ALTER TABLE `event_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_reservations`
--
ALTER TABLE `event_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `package_prices`
--
ALTER TABLE `package_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `package_pricing`
--
ALTER TABLE `package_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `room_prices`
--
ALTER TABLE `room_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_pricing`
--
ALTER TABLE `room_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dining_reservations`
--
ALTER TABLE `dining_reservations`
  ADD CONSTRAINT `dining_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_dining_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_reservations`
--
ALTER TABLE `event_reservations`
  ADD CONSTRAINT `event_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_event_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD CONSTRAINT `fk_room_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

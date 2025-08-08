<?php
session_start();
require_once './config/database.php';

// Process room booking form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $guests = (int)($_POST['guests'] ?? 0);
    $checkIn = $_POST['check-in'] ?? '';
    $checkOut = $_POST['check-out'] ?? '';
    $roomType = sanitize($_POST['room-type'] ?? '');
    $packageType = sanitize($_POST['package-type'] ?? '');
    $specialRequests = sanitize($_POST['requests'] ?? '');

    // Validate required fields
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }

    if ($guests <= 0) {
        $errors[] = "Number of guests is required";
    }

    if (empty($checkIn)) {
        $errors[] = "Check-in date is required";
    }

    if (empty($checkOut)) {
        $errors[] = "Check-out date is required";
    }

    if (empty($roomType)) {
        $errors[] = "Room type is required";
    }

    if (empty($packageType)) {
        $errors[] = "Package type is required";
    }

    // Validate dates
    if (!empty($checkIn) && !empty($checkOut)) {
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $today = new DateTime();

        if ($checkInDate < $today) {
            $errors[] = "Check-in date cannot be in the past";
        }

        if ($checkOutDate <= $checkInDate) {
            $errors[] = "Check-out date must be after check-in date";
        }
    }

    // If no errors, process the booking
    if (empty($errors)) {
        try {
            $db = getDB();

            // Room prices and package prices
            $roomPrices = [
                'deluxe' => 150,
                'suite' => 250,
                'presidential' => 500
            ];

            $packagePrices = [
                'individual' => 120,
                'couple' => 200,
                'family' => 300
            ];

            // Calculate nights
            $nights = (new DateTime($checkOut))->diff(new DateTime($checkIn))->days;

            // Calculate costs
            $roomCost = $roomPrices[$roomType] * $nights;
            $packageCost = $packagePrices[$packageType] * $nights;

            $totalCost = $roomCost + $packageCost;

            // Generate booking reference
            $bookingRef = 'BOOK' . strtoupper(substr(md5(uniqid()), 0, 6));

            // Get user ID if logged in
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            // Insert booking into database
            $stmt = $db->prepare("
                INSERT INTO room_bookings (
                    user_id, booking_reference, guest_name, email, phone, guests,
                    check_in_date, check_out_date, room_type, package_type,
                    special_requests, room_cost, package_cost, total_cost, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $user_id,
                $bookingRef,
                $name,
                $email,
                $phone,
                $guests,
                $checkInDate->format('Y-m-d'),
                $checkOutDate->format('Y-m-d'),
                $roomType,
                $packageType,
                $specialRequests,
                $roomCost,
                $packageCost,
                $totalCost,
                'pending'
            ]);

            // Create success message
            $success_message = "Thank you for your booking! Your reservation is pending. Booking Reference: $bookingRef. We will contact you to confirm your reservation.";
            redirectWithMessage('booking.php', 'success', $success_message);
            
        } catch (PDOException $e) {
            $error_message = "Database error. Please try again later.";
            redirectWithMessage('booking.php', 'error', $error_message);
        }
    } else {
        // Redirect back with errors
        $error_message = implode(", ", $errors);
        redirectWithMessage('booking.php', 'error', $error_message);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay - DULUX</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .booking-page {
            padding-top: 100px;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .booking-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .booking-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        .booking-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .booking-content {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 2rem;
        }

        .back-btn {
            position: fixed;
            top: 120px;
            left: 2rem;
            background: white;
            color: #2c3e50;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Alert Styles */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <div class="logo-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="logo-text">
                    <h2>DULUX</h2>
                    <span>Luxury Hotel</span>
                </div>
            </a>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="index.php#rooms" class="nav-link">Rooms</a></li>
                <li><a href="index.php#packages" class="nav-link">Packages</a></li>
                <li><a href="index.php#amenities" class="nav-link">Amenities</a></li>
                <li><a href="index.php#contact" class="nav-link">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link">Reservations <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="booking.php"><i class="fas fa-bed"></i> Room Booking</a></li>
                        <li><a href="dining.php"><i class="fas fa-utensils"></i> Dining Reservation</a></li>
                        <li><a href="events.php"><i class="fas fa-glass-cheers"></i> Event Reservation</a></li>
                    </ul>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="dropdown">
                        <a href="#" class="nav-link">My Account <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="my_bookings.php"><i class="fas fa-list"></i> My Bookings</a></li>
                            <li><a href="my_dining.php"><i class="fas fa-utensils"></i> My Dining</a></li>
                            <li><a href="my_events.php"><i class="fas fa-glass-cheers"></i> My Events</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                    <li><a href="register.php" class="nav-link">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Back Button -->
    <a href="index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <!-- Booking Page -->
    <section class="booking-page">
        <div class="booking-container">
            <div class="booking-header">
                <h1>Book Your Perfect Stay</h1>
                <p>Choose your room, package, and extra amenities for an unforgettable experience</p>
            </div>
            
            <div class="booking-content">
                <?php
                // Display success or error messages
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message'] ?? 'Booking submitted successfully!') . '</div>';
                }
                if (isset($_GET['error']) && $_GET['error'] == '1') {
                    echo '<div class="alert alert-error">' . htmlspecialchars($_GET['message'] ?? 'An error occurred. Please try again.') . '</div>';
                }
                ?>
                <form class="booking-form" id="bookingForm" action="booking.php" method="POST">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Personal Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="guests">Number of Guests *</label>
                                <select id="guests" name="guests" required>
                                    <option value="">Select Guests</option>
                                    <option value="1">1 Guest</option>
                                    <option value="2">2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5+ Guests</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="form-section">
                        <h3><i class="fas fa-calendar"></i> Stay Dates</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="check-in">Check-in Date *</label>
                                <input type="date" id="check-in" name="check-in" required>
                            </div>
                            <div class="form-group">
                                <label for="check-out">Check-out Date *</label>
                                <input type="date" id="check-out" name="check-out" required>
                            </div>
                        </div>
                    </div>

                    <!-- Room Selection -->
                    <div class="form-section">
                        <h3><i class="fas fa-bed"></i> Choose Your Room</h3>
                        <div class="form-group">
                            <label for="room-type">Room Type *</label>
                            <select id="room-type" name="room-type" required>
                                <option value="">Select Room Type</option>
                                <option value="deluxe">Deluxe Room - $150/night</option>
                                <option value="suite">Executive Suite - $250/night</option>
                                <option value="presidential">Presidential Suite - $500/night</option>
                            </select>
                        </div>
                    </div>

                    <!-- Package Selection -->
                    <div class="form-section">
                        <h3><i class="fas fa-gift"></i> Choose Your Package</h3>
                        <div class="form-group">
                            <label for="package-type">Package Type *</label>
                            <select id="package-type" name="package-type" required>
                                <option value="">Select Package</option>
                                <option value="individual">Individual Package - $120/night</option>
                                <option value="couple">Couple Package - $200/night</option>
                                <option value="family">Family Package - $300/night</option>
                            </select>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="form-section">
                        <h3><i class="fas fa-comment"></i> Special Requests</h3>
                        <div class="form-group">
                            <label for="requests">Any special requests or preferences?</label>
                            <textarea id="requests" name="requests" rows="4" placeholder="Tell us about any special requirements..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-check"></i> Confirm Booking
                    </button>
                </form>
            </div>
        </div>
    </section>

</body>
</html>

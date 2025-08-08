<?php
session_start();
require_once './config/database.php';

// Process dining reservation form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $guests = (int)($_POST['guests'] ?? 0);
    $reservationDate = $_POST['reservation-date'] ?? '';
    $reservationTime = $_POST['reservation-time'] ?? '';
    $restaurant = sanitize($_POST['restaurant'] ?? '');
    $occasion = sanitize($_POST['occasion'] ?? '');
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

    if (empty($reservationDate)) {
        $errors[] = "Reservation date is required";
    }

    if (empty($reservationTime)) {
        $errors[] = "Reservation time is required";
    }

    if (empty($restaurant)) {
        $errors[] = "Restaurant is required";
    }

    // If no errors, process the reservation
    if (empty($errors)) {
        try {
            $db = getDB();

            // Calculate total price based on restaurant selection (hardcoded prices for each restaurant)
            $restaurantPrices = [
                'la-vista' => 50,  // Example price for La Vista
                'cafe-serenity' => 40, // Example price for Cafe Serenity
                'sky-lounge' => 60, // Example price for Sky Lounge
            ];

            $pricePerGuest = $restaurantPrices[$restaurant] ?? 0;
            $totalPrice = $pricePerGuest * $guests;

            // Generate reservation reference (optional)
            $reservationRef = 'DIN' . strtoupper(substr(md5(uniqid()), 0, 6));

            // Get user ID if logged in
            $user_id = getCurrentUserId();

            // Insert reservation into the database
            $stmt = $db->prepare("
                INSERT INTO dining_reservations (
                    user_id, guest_name, email, phone, guests,
                    reservation_date, reservation_time, restaurant, occasion,
                    special_requests, status, price
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $user_id,
                $name,
                $email,
                $phone,
                $guests,
                $reservationDate,
                $reservationTime,
                $restaurant,
                $occasion,
                $specialRequests,
                'pending',  // Default status
                $totalPrice
            ]);

            // Log the reservation activity
            if ($user_id) {
                logActivity($user_id, 'dining_reservation', "Reserved at $restaurant for $guests guests on $reservationDate");
            }

            // Create success message
            $success_message = "Thank you for your dining reservation! Your reservation is pending. We will contact you to confirm your reservation.";
            redirectWithMessage('dining.php', 'success', $success_message);

        } catch (PDOException $e) {
            $error_message = "Database error. Please try again later.";
            redirectWithMessage('dining.php', 'error', $error_message);
        }
    } else {
        // Redirect back with errors
        $error_message = implode(", ", $errors);
        redirectWithMessage('dining.php', 'error', $error_message);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine at DULUX</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dining-page {
            padding-top: 100px;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .dining-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dining-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        .dining-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .dining-content {
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

    <!-- Dining Reservation Page -->
    <section class="dining-page">
        <div class="dining-container">
            <div class="dining-header">
                <h1>Reserve Your Dining Experience</h1>
                <p>Choose your meal, time, and any extra services for an unforgettable dining experience</p>
            </div>

            <div class="dining-content">
                <?php
                // Display success or error messages
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message'] ?? 'Dining reservation submitted successfully!') . '</div>';
                }
                if (isset($_GET['error']) && $_GET['error'] == '1') {
                    echo '<div class="alert alert-error">' . htmlspecialchars($_GET['message'] ?? 'An error occurred. Please try again.') . '</div>';
                }
                ?>

                <form class="dining-form" id="diningForm" action="dining.php" method="POST">
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

                    <!-- Dining Date and Time -->
                    <div class="form-section">
                        <h3><i class="fas fa-calendar"></i> Dining Date and Time</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="reservation-date">Reservation Date *</label>
                                <input type="date" id="reservation-date" name="reservation-date" required>
                            </div>
                            <div class="form-group">
                                <label for="reservation-time">Reservation Time *</label>
                                <input type="time" id="reservation-time" name="reservation-time" required>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurant Selection -->
                    <div class="form-section">
                        <h3><i class="fas fa-utensils"></i> Choose Your Restaurant</h3>
                        <div class="form-group">
                            <label for="restaurant">Restaurant *</label>
                            <select id="restaurant" name="restaurant" required>
                                <option value="">Select Restaurant</option>
                                <option value="la-vista">La Vista</option>
                                <option value="cafe-serenity">Cafe Serenity</option>
                                <option value="sky-lounge">Sky Lounge</option>
                            </select>
                        </div>
                    </div>

                    <!-- Occasion Selection -->
                    <div class="form-section">
                        <h3><i class="fas fa-gift"></i> Occasion</h3>
                        <div class="form-group">
                            <label for="occasion">Select Occasion</label>
                            <select id="occasion" name="occasion">
                                <option value="">Select Occasion</option>
                                <option value="birthday">Birthday</option>
                                <option value="anniversary">Anniversary</option>
                                <option value="business">Business</option>
                                <option value="romantic">Romantic</option>
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
                        <i class="fas fa-check"></i> Confirm Reservation
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script src="js/script.js"></script>
</body>
</html>

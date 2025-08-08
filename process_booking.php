<?php
session_start();
require_once 'config/database.php';

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
    $amenities = $_POST['amenities'] ?? [];
    $requests = sanitize($_POST['requests'] ?? '');

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
            
            // Calculate total price
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

            $amenityPrices = [
                'pool' => 20,
                'spa' => 80,
                'transfer' => 50,
                'room-service' => 15
            ];

            // Calculate nights
            $nights = (new DateTime($checkOut))->diff(new DateTime($checkIn))->days;

            // Calculate costs
            $roomCost = $roomPrices[$roomType] * $nights;
            $packageCost = $packagePrices[$packageType] * $nights;
            $amenitiesCost = 0;

            foreach ($amenities as $amenity) {
                if (isset($amenityPrices[$amenity])) {
                    $amenitiesCost += $amenityPrices[$amenity];
                }
            }

            $totalCost = $roomCost + $packageCost + $amenitiesCost;

            // Generate booking reference
            $bookingRef = generateBookingReference();

            // Get user ID if logged in
            $user_id = getCurrentUserId();

            // Insert booking into database
            $stmt = $db->prepare("
    INSERT INTO room_bookings (
        user_id, booking_reference, guest_name, email, phone, guests,
        check_in_date, check_out_date, room_type, package_type,
        amenities, special_requests, room_cost, package_cost, amenities_cost, total_cost, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                implode(', ', $amenities),
                $requests,
                $roomCost,
                $packageCost,
                $amenitiesCost,
                $totalCost,
                'pending'
            ]);

            $booking_id = $db->lastInsertId();

            // Log the booking activity
            if ($user_id) {
                logActivity($user_id, 'room_booking', "Booked $roomType room for $nights nights");
            }

            // Create success message
            $amenities_text = !empty($amenities) ? " with amenities: " . implode(", ", $amenities) : "";
            $success_message = "Thank you for your booking! Your reservation is <b>pending</b> and will be reviewed by our team. Booking Reference: $bookingRef. $guests guests, " . ucfirst($roomType) . " room with " . ucfirst($packageType) . " package for $nights nights" . $amenities_text . ". Total cost: $" . number_format($totalCost, 2) . ". We will contact you at $email to confirm your stay.";
            
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
} else {
    // If not POST request, redirect to booking page
    header("Location: booking.php");
    exit();
}

// Helper functions

// Sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate booking reference
function generateBookingReference() {
    return 'DULUX' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
}

// Get current user ID (if logged in)
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Log activity
function logActivity($user_id, $action, $details = '') {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $details]);
}

// Redirect with message
function redirectWithMessage($url, $type, $message) {
    header("Location: $url?$type=1&message=" . urlencode($message));
    exit();
}
?>

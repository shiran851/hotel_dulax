<?php
session_start();
require_once 'config/database.php';

// Process dining reservation form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $guests = (int)($_POST['guests'] ?? 0);
    $date = $_POST['date'] ?? '';
    $time = sanitize($_POST['time'] ?? '');
    $restaurant = sanitize($_POST['restaurant'] ?? '');
    $requests = sanitize($_POST['requests'] ?? '');
    $occasion = sanitize($_POST['occasion'] ?? '');

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
    
    if (empty($date)) {
        $errors[] = "Reservation date is required";
    }
    
    if (empty($time)) {
        $errors[] = "Reservation time is required";
    }
    
    if (empty($restaurant)) {
        $errors[] = "Restaurant selection is required";
    }

    // Validate date
    if (!empty($date)) {
        $reservationDate = new DateTime($date);
        $today = new DateTime();
        
        if ($reservationDate < $today) {
            $errors[] = "Reservation date cannot be in the past";
        }
    }

    // If no errors, process the reservation
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Get user ID if logged in
            $user_id = getCurrentUserId();

            // Insert dining reservation into database
            $stmt = $db->prepare("
                INSERT INTO dining_reservations (
                    user_id, guest_name, email, phone, guests, reservation_date,
                    reservation_time, restaurant, occasion, special_requests
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $user_id,
                $name,
                $email,
                $phone,
                $guests,
                $date,
                $time,
                $restaurant,
                $occasion,
                $requests
            ]);

            $reservation_id = $db->lastInsertId();

            // Log the dining reservation activity
            if ($user_id) {
                logActivity($user_id, 'dining_reservation', "Made dining reservation at $restaurant for $guests guests");
            }

            // Create success message
            $success_message = "Thank you for your dining reservation request! We have received your booking for $guests guests at $restaurant on $date at $time. We will contact you at $email to confirm your reservation.";
            
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
} else {
    // If not POST request, redirect to dining page
    header("Location: dining.php");
    exit();
}
?> 
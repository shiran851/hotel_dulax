<?php
session_start();
require_once 'config/database.php';

// Process event reservation form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $event_type = sanitize($_POST['event-type'] ?? '');
    $guests = sanitize($_POST['guests'] ?? '');
    $date = $_POST['date'] ?? '';
    $time = sanitize($_POST['time'] ?? '');
    $venue = sanitize($_POST['venue'] ?? '');
    $services = $_POST['services'] ?? [];
    $budget = sanitize($_POST['budget'] ?? '');
    $requirements = sanitize($_POST['requirements'] ?? '');

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
    
    if (empty($event_type)) {
        $errors[] = "Event type is required";
    }
    
    if (empty($guests)) {
        $errors[] = "Number of guests is required";
    }
    
    if (empty($date)) {
        $errors[] = "Event date is required";
    }
    
    if (empty($time)) {
        $errors[] = "Event time is required";
    }
    
    if (empty($venue)) {
        $errors[] = "Venue selection is required";
    }

    // Validate date
    if (!empty($date)) {
        $eventDate = new DateTime($date);
        $today = new DateTime();
        
        if ($eventDate < $today) {
            $errors[] = "Event date cannot be in the past";
        }
    }

    // If no errors, process the reservation
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Get user ID if logged in
            $user_id = getCurrentUserId();

            // Insert event reservation into database
            $stmt = $db->prepare("
                INSERT INTO event_reservations (
                    user_id, guest_name, email, phone, company, event_type,
                    guests_range, event_date, event_time, venue, services, budget_range, requirements
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $user_id,
                $name,
                $email,
                $phone,
                $company,
                $event_type,
                $guests,
                $date,
                $time,
                $venue,
                implode(', ', $services),
                $budget,
                $requirements
            ]);

            $reservation_id = $db->lastInsertId();

            // Log the event reservation activity
            if ($user_id) {
                logActivity($user_id, 'event_reservation', "Made event reservation for $event_type at $venue for $guests guests");
            }

            // Create success message
            $success_message = "Thank you for your event reservation request! We have received your booking and will contact you to confirm your reservation.";
            
            redirectWithMessage('events.php', 'success', $success_message);
            
        } catch (PDOException $e) {
            $error_message = "Database error. Please try again later.";
            redirectWithMessage('events.php', 'error', $error_message);
        }
    } else {
        // Redirect back with errors
        $error_message = implode(", ", $errors);
        redirectWithMessage('events.php', 'error', $error_message);
    }
} else {
    // If not POST request, redirect to events page
    header("Location: events.php");
    exit();
}
?> 
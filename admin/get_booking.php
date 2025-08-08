<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['booking_id'])) {
    $booking_id = (int)$_GET['booking_id'];
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, guest_name, email, phone, room_type, guests, check_in, check_out, total_cost, status, booking_reference FROM room_bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if ($booking) {
            echo json_encode($booking);
        } else {
            echo json_encode(['error' => 'Booking not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?> 
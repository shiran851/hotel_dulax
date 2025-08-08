<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

try {
    $db = getDB();
    
    // Get all data for report
    $stmt = $db->query("SELECT * FROM room_bookings WHERE status = 'completed' ORDER BY created_at DESC");
    $completed_bookings = $stmt->fetchAll() ?: [];
    
    $stmt = $db->query("SELECT * FROM dining_reservations WHERE status = 'completed' ORDER BY created_at DESC");
    $completed_dining = $stmt->fetchAll() ?: [];
    
    $stmt = $db->query("SELECT * FROM event_reservations WHERE status = 'completed' ORDER BY created_at DESC");
    $completed_events = $stmt->fetchAll() ?: [];

// Calculate analytics
$total_revenue = array_sum(array_column($completed_bookings, 'total_cost'));
$total_bookings = count($completed_bookings);
$total_dining = count($completed_dining);
$total_events = count($completed_events);

$dining_revenue = 0;
foreach ($completed_dining as $dining) {
    $price_per_guest = ['la-vista' => 50, 'cafe-serenity' => 40, 'sky-lounge' => 60];
    $dining_revenue += ($price_per_guest[$dining['restaurant']] ?? 50) * $dining['guests'];
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="DULUX_Analytics_Report_' . date('Y-m-d') . '.csv"');

// Create CSV content
$output = fopen('php://output', 'w');

// Report header
fputcsv($output, ['DULUX HOTEL - ANALYTICS REPORT']);
fputcsv($output, ['Generated on: ' . date('F j, Y g:i A')]);
fputcsv($output, []);

// Summary statistics
fputcsv($output, ['SUMMARY STATISTICS']);
fputcsv($output, ['Total Revenue', '$' . number_format($total_revenue + $dining_revenue, 2)]);
fputcsv($output, ['Booking Revenue', '$' . number_format($total_revenue, 2)]);
fputcsv($output, ['Dining Revenue', '$' . number_format($dining_revenue, 2)]);
fputcsv($output, ['Completed Bookings', $total_bookings]);
fputcsv($output, ['Completed Dining', $total_dining]);
fputcsv($output, ['Completed Events', $total_events]);
fputcsv($output, ['Total Completed Tasks', $total_bookings + $total_dining + $total_events]);
fputcsv($output, []);

// Completed Bookings
fputcsv($output, ['COMPLETED ROOM BOOKINGS']);
fputcsv($output, ['Guest Name', 'Email', 'Room Type', 'Package', 'Check-in', 'Check-out', 'Guests', 'Total Cost', 'Completed Date']);
foreach ($completed_bookings as $booking) {
    fputcsv($output, [
        $booking['guest_name'],
        $booking['email'],
        ucfirst($booking['room_type']),
        ucfirst($booking['package_type']),
        $booking['check_in_date'],
        $booking['check_out_date'],
        $booking['guests'],
        '$' . number_format($booking['total_cost'], 2),
        date('M j, Y', strtotime($booking['created_at']))
    ]);
}
fputcsv($output, []);

// Completed Dining
fputcsv($output, ['COMPLETED DINING RESERVATIONS']);
fputcsv($output, ['Guest Name', 'Email', 'Restaurant', 'Date', 'Time', 'Guests', 'Revenue', 'Completed Date']);
foreach ($completed_dining as $dining) {
    $price_per_guest = ['la-vista' => 50, 'cafe-serenity' => 40, 'sky-lounge' => 60];
    $revenue = ($price_per_guest[$dining['restaurant']] ?? 50) * $dining['guests'];
    fputcsv($output, [
        $dining['guest_name'],
        $dining['email'],
        ucfirst(str_replace('-', ' ', $dining['restaurant'])),
        $dining['reservation_date'],
        $dining['reservation_time'],
        $dining['guests'],
        '$' . number_format($revenue, 2),
        date('M j, Y', strtotime($dining['created_at']))
    ]);
}
fputcsv($output, []);

// Completed Events
fputcsv($output, ['COMPLETED EVENT RESERVATIONS']);
fputcsv($output, ['Guest Name', 'Email', 'Event Type', 'Venue', 'Date', 'Guests', 'Completed Date']);
foreach ($completed_events as $event) {
    fputcsv($output, [
        $event['guest_name'],
        $event['email'],
        ucfirst($event['event_type']),
        ucfirst(str_replace('-', ' ', $event['venue'])),
        $event['event_date'],
        $event['guests_range'],
        date('M j, Y', strtotime($event['created_at']))
    ]);
}

    fclose($output);
} catch (Exception $e) {
    header("Location: reports.php?error=1&message=" . urlencode("Error generating report: " . $e->getMessage()));
}
exit();
?>
<?php
session_start();
require_once 'config/database.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$db = getDB();
$user_id = getCurrentUserId();

// Get user's bookings
$stmt = $db->prepare("SELECT * FROM room_bookings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

// Get user's dining reservations
$stmt = $db->prepare("SELECT * FROM dining_reservations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$dining = $stmt->fetchAll();

// Get user's event reservations
$stmt = $db->prepare("SELECT * FROM event_reservations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - DULUX</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .user-dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            padding-top: 120px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .tabs {
            display: flex;
            background: white;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            background: #e74c3c;
            color: white;
        }
        
        .tab-content {
            display: none;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        .reservation-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid #e74c3c;
        }
        
        .reservation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-weight: 500;
            color: #333;
        }
        
        .no-reservations {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-reservations i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
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
                <li><a href="booking.php" class="nav-link">Book Room</a></li>
                <li><a href="dining.php" class="nav-link">Dining</a></li>
                <li><a href="events.php" class="nav-link">Events</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link">My Account <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="my_reservations.php" class="active"><i class="fas fa-list"></i> All Reservations</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="user-dashboard">
        <div class="dashboard-header">
            <h1><i class="fas fa-calendar-check"></i> My Reservations</h1>
            <p>View all your bookings and reservation status</p>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('bookings')">
                <i class="fas fa-bed"></i> Room Bookings (<?php echo count($bookings); ?>)
            </div>
            <div class="tab" onclick="showTab('dining')">
                <i class="fas fa-utensils"></i> Dining (<?php echo count($dining); ?>)
            </div>
            <div class="tab" onclick="showTab('events')">
                <i class="fas fa-glass-cheers"></i> Events (<?php echo count($events); ?>)
            </div>
        </div>

        <!-- Room Bookings Tab -->
        <div id="bookings" class="tab-content active">
            <?php if (empty($bookings)): ?>
                <div class="no-reservations">
                    <i class="fas fa-bed"></i>
                    <h3>No room bookings found</h3>
                    <p>You haven't made any room bookings yet.</p>
                    <a href="booking.php" class="btn btn-primary">Book a Room</a>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <h3><?php echo htmlspecialchars($booking['booking_reference']); ?></h3>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                        <div class="reservation-details">
                            <div class="detail-item">
                                <span class="detail-label">Room Type</span>
                                <span class="detail-value"><?php echo ucfirst($booking['room_type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Package</span>
                                <span class="detail-value"><?php echo ucfirst($booking['package_type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-in</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-out</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Guests</span>
                                <span class="detail-value"><?php echo $booking['guests']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Total Cost</span>
                                <span class="detail-value">$<?php echo number_format($booking['total_cost'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Dining Tab -->
        <div id="dining" class="tab-content">
            <?php if (empty($dining)): ?>
                <div class="no-reservations">
                    <i class="fas fa-utensils"></i>
                    <h3>No dining reservations found</h3>
                    <p>You haven't made any dining reservations yet.</p>
                    <a href="dining.php" class="btn btn-primary">Reserve a Table</a>
                </div>
            <?php else: ?>
                <?php foreach ($dining as $reservation): ?>
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <h3><?php echo ucfirst(str_replace('-', ' ', $reservation['restaurant'])); ?></h3>
                            <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                <?php echo ucfirst($reservation['status']); ?>
                            </span>
                        </div>
                        <div class="reservation-details">
                            <div class="detail-item">
                                <span class="detail-label">Date</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Time</span>
                                <span class="detail-value"><?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Guests</span>
                                <span class="detail-value"><?php echo $reservation['guests']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Occasion</span>
                                <span class="detail-value"><?php echo $reservation['occasion'] ? ucfirst($reservation['occasion']) : 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Events Tab -->
        <div id="events" class="tab-content">
            <?php if (empty($events)): ?>
                <div class="no-reservations">
                    <i class="fas fa-glass-cheers"></i>
                    <h3>No event reservations found</h3>
                    <p>You haven't made any event reservations yet.</p>
                    <a href="events.php" class="btn btn-primary">Plan an Event</a>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <h3><?php echo ucfirst($event['event_type']); ?> Event</h3>
                            <span class="status-badge status-<?php echo $event['status']; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                        <div class="reservation-details">
                            <div class="detail-item">
                                <span class="detail-label">Venue</span>
                                <span class="detail-value"><?php echo ucfirst(str_replace('-', ' ', $event['venue'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Time</span>
                                <span class="detail-value"><?php echo ucfirst($event['event_time']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Guests</span>
                                <span class="detail-value"><?php echo $event['guests_range']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Budget</span>
                                <span class="detail-value"><?php echo $event['budget_range'] ? ucfirst(str_replace('-', ' - $', $event['budget_range'])) : 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
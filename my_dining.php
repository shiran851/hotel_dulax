<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$db = getDB();
$user_id = getCurrentUserId();

// Get user's dining reservations
$stmt = $db->prepare("SELECT * FROM dining_reservations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$dining_reservations = $stmt->fetchAll();

// Get user info
$stmt = $db->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dining -DULUX </title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .user-dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .welcome-message {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .dining-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .dining-section h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .reservation-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid #e74c3c;
            transition: transform 0.3s ease;
        }
        
        .reservation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .reservation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .reservation-id {
            font-weight: 600;
            color: #e74c3c;
            font-size: 1.1rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
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
            margin-bottom: 1rem;
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
        
        .reservation-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #e74c3c;
            color: white;
        }
        
        .btn-primary:hover {
            background: #c0392b;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin: 0;
            color: #e74c3c;
        }
        
        .stat-card p {
            margin: 0.5rem 0 0 0;
            color: #666;
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
                        <li><a href="my_bookings.php"><i class="fas fa-list"></i> My Bookings</a></li>
                        <li><a href="my_dining.php" class="active"><i class="fas fa-utensils"></i> My Dining</a></li>
                        <li><a href="my_events.php"><i class="fas fa-glass-cheers"></i> My Events</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <div class="user-dashboard">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-utensils"></i> My Dining Reservations</h1>
            <p>View and manage your dining reservations</p>
        </div>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <h2>Welcome back, <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>!</h2>
            <p>Here are all your dining reservations and their current status.</p>
        </div>

        <!-- Statistics -->
        <?php if (!empty($dining_reservations)): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo count($dining_reservations); ?></h3>
                    <p>Total Reservations</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($dining_reservations, function($d) { return $d['status'] == 'confirmed'; })); ?></h3>
                    <p>Confirmed Reservations</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($dining_reservations, function($d) { return $d['status'] == 'pending'; })); ?></h3>
                    <p>Pending Reservations</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_unique(array_column($dining_reservations, 'restaurant'))); ?></h3>
                    <p>Restaurants Visited</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Dining Section -->
        <div class="dining-section">
            <h2><i class="fas fa-list"></i> My Dining Reservations</h2>
            
            <?php if (empty($dining_reservations)): ?>
                <div class="no-reservations">
                    <i class="fas fa-utensils"></i>
                    <h3>No dining reservations found</h3>
                    <p>You haven't made any dining reservations yet.</p>
                    <a href="dining.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Make a Reservation
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($dining_reservations as $reservation): ?>
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <span class="reservation-id">Reservation #<?php echo $reservation['id']; ?></span>
                            <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                <?php echo ucfirst($reservation['status']); ?>
                            </span>
                        </div>
                        
                        <div class="reservation-details">
                            <div class="detail-item">
                                <span class="detail-label">Restaurant</span>
                                <span class="detail-value"><?php echo ucfirst(str_replace('-', ' ', $reservation['restaurant'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Time</span>
                                <span class="detail-value"><?php echo $reservation['reservation_time'] ? date('g:i A', strtotime($reservation['reservation_time'])) : 'No time specified'; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Guests</span>
                                <span class="detail-value"><?php echo htmlspecialchars($reservation['guests']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Occasion</span>
                                <span class="detail-value"><?php echo ucfirst($reservation['occasion'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Reserved On</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($reservation['created_at'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="reservation-actions">
                            <a href="dining.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Make Another Reservation
                            </a>
                            <?php if ($reservation['status'] == 'pending'): ?>
                                <span class="btn btn-secondary" style="cursor: default;">
                                    <i class="fas fa-clock"></i> Awaiting Confirmation
                                </span>
                            <?php elseif ($reservation['status'] == 'confirmed'): ?>
                                <span class="btn btn-secondary" style="cursor: default; background: #28a745;">
                                    <i class="fas fa-check"></i> Confirmed
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html> 
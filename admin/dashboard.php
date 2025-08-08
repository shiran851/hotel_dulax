<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$db = getDB();

// Get statistics
$stats = [];

// Total users
$stmt = $db->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];

// Total bookings
$stmt = $db->query("SELECT COUNT(*) as total FROM room_bookings");
$stats['bookings'] = $stmt->fetch()['total'];

// Total dining reservations
$stmt = $db->query("SELECT COUNT(*) as total FROM dining_reservations");
$stats['dining'] = $stmt->fetch()['total'];

// Total event reservations
$stmt = $db->query("SELECT COUNT(*) as total FROM event_reservations");
$stats['events'] = $stmt->fetch()['total'];

// Revenue calculations
$stmt = $db->query("SELECT SUM(total_cost) as total_revenue FROM room_bookings WHERE status = 'confirmed'");
$stats['revenue'] = $stmt->fetch()['total_revenue'] ?? 0;

// Recent bookings
$stmt = $db->query("SELECT * FROM room_bookings ORDER BY created_at DESC LIMIT 5");
$recent_bookings = $stmt->fetchAll();

// Recent dining reservations
$stmt = $db->query("SELECT * FROM dining_reservations ORDER BY created_at DESC LIMIT 5");
$recent_dining = $stmt->fetchAll();

// Recent event reservations
$stmt = $db->query("SELECT * FROM event_reservations ORDER BY created_at DESC LIMIT 5");
$recent_events = $stmt->fetchAll();

// Get pending bookings count
$stmt = $db->query("SELECT COUNT(*) as pending FROM room_bookings WHERE status = 'pending'");
$stats['pending_bookings'] = $stmt->fetch()['pending'];

// Get confirmed bookings count
$stmt = $db->query("SELECT COUNT(*) as confirmed FROM room_bookings WHERE status = 'confirmed'");
$stats['confirmed_bookings'] = $stmt->fetch()['confirmed'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DULUX</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
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
        
        .stat-card i {
            font-size: 2rem;
            color: #e74c3c;
            margin-bottom: 0.5rem;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin: 0;
            color: #2c3e50;
        }
        
        .stat-card p {
            color: #666;
            margin: 0;
        }
        
        .admin-nav {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .admin-nav a {
            color: #e74c3c;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-nav a:hover {
            background: #f8f9fa;
        }
        
        .admin-nav a.active {
            background: #e74c3c;
            color: white;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            margin-left: auto;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .section h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .reservation-item {
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease;
        }
        
        .reservation-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .reservation-info h4 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
        }
        
        .reservation-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
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
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .action-btn i {
            font-size: 1.5rem;
        }
        
        .chart-container {
            height: 300px;
            margin-top: 1rem;
        }
        
        .revenue-highlight {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .revenue-highlight h3 {
            font-size: 2.5rem;
            margin: 0;
        }
        
        .revenue-highlight p {
            margin: 0;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-nav {
                flex-direction: column;
            }
            
            .logout-btn {
                margin-left: 0;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-nav">
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="bookings.php"><i class="fas fa-bed"></i> Bookings</a>
            <a href="dining.php"><i class="fas fa-utensils"></i> Dining</a>
            <a href="events.php"><i class="fas fa-glass-cheers"></i> Events</a>
            <a href="pricing.php"><i class="fas fa-dollar-sign"></i> Pricing</a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="admin-header">
            <h1><i class="fas fa-crown"></i> DULUX Admin Dashboard</h1>
            <p>Welcome back, <?php echo $_SESSION['user_name'] ?? 'Admin'; ?>! Here's your overview.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?php echo $stats['users']; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-bed"></i>
                <h3><?php echo $stats['bookings']; ?></h3>
                <p>Room Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-utensils"></i>
                <h3><?php echo $stats['dining']; ?></h3>
                <p>Dining Reservations</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-glass-cheers"></i>
                <h3><?php echo $stats['events']; ?></h3>
                <p>Event Reservations</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $stats['pending_bookings']; ?></h3>
                <p>Pending Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?php echo $stats['confirmed_bookings']; ?></h3>
                <p>Confirmed Bookings</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <div class="section">
                    <h2><i class="fas fa-chart-line"></i> Revenue Overview</h2>
                    <div class="revenue-highlight">
                        <h3>$<?php echo number_format($stats['revenue'], 2); ?></h3>
                        <p>Total Revenue from Confirmed Bookings</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="section">
                    <h2><i class="fas fa-bed"></i> Recent Room Bookings</h2>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <div class="reservation-item">
                            <div class="reservation-info">
                                <h4><?php echo htmlspecialchars($booking['guest_name']); ?></h4>
                                <p>Booking Ref: <?php echo htmlspecialchars($booking['booking_reference']); ?> | 
                                   <?php echo htmlspecialchars($booking['room_type']); ?> Room | 
                                   <?php echo htmlspecialchars($booking['guests']); ?> guests | 
                                   $<?php echo number_format($booking['total_cost'], 2); ?></p>
                            </div>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="section">
                    <h2><i class="fas fa-utensils"></i> Recent Dining Reservations</h2>
                    <?php foreach ($recent_dining as $dining): ?>
                        <div class="reservation-item">
                            <div class="reservation-info">
                                <h4><?php echo htmlspecialchars($dining['guest_name']); ?></h4>
                                <p><?php echo htmlspecialchars($dining['restaurant']); ?> | 
                                   <?php echo htmlspecialchars($dining['guests']); ?> guests | 
                                   <?php echo htmlspecialchars($dining['reservation_date']); ?> at 
                                   <?php echo htmlspecialchars($dining['reservation_time']); ?></p>
                            </div>
                            <span class="status-badge status-<?php echo $dining['status']; ?>">
                                <?php echo ucfirst($dining['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sidebar">
                <div class="section">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="users.php" class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Add User</span>
                        </a>
                        <a href="bookings.php" class="action-btn">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Manage Bookings</span>
                        </a>
                        <a href="pricing.php" class="action-btn">
                            <i class="fas fa-dollar-sign"></i>
                            <span>Update Pricing</span>
                        </a>
                        <a href="reports.php" class="action-btn">
                            <i class="fas fa-file-alt"></i>
                            <span>Generate Reports</span>
                        </a>
                    </div>
                </div>

                <div class="section">
                    <h2><i class="fas fa-glass-cheers"></i> Recent Event Reservations</h2>
                    <?php foreach ($recent_events as $event): ?>
                        <div class="reservation-item">
                            <div class="reservation-info">
                                <h4><?php echo htmlspecialchars($event['guest_name']); ?></h4>
                                <p><?php echo htmlspecialchars($event['event_type']); ?> | 
                                   <?php echo htmlspecialchars($event['venue']); ?> | 
                                   <?php echo htmlspecialchars($event['guests_range']); ?> guests | 
                                   <?php echo htmlspecialchars($event['event_date']); ?></p>
                            </div>
                            <span class="status-badge status-<?php echo $event['status']; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="section">
                    <h2><i class="fas fa-chart-pie"></i> System Status</h2>
                    <div style="display: grid; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Database Connection</span>
                            <span style="color: #28a745;">✅ Online</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>PHP Version</span>
                            <span><?php echo phpversion(); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Server Time</span>
                            <span><?php echo date('Y-m-d H:i:s'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 
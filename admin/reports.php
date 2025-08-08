<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$db = getDB();

// Get completed bookings
$stmt = $db->query("SELECT * FROM room_bookings WHERE status = 'completed' ORDER BY updated_at DESC");
$completed_bookings = $stmt->fetchAll();

// Get completed dining reservations
$stmt = $db->query("SELECT * FROM dining_reservations WHERE status = 'completed' ORDER BY updated_at DESC");
$completed_dining = $stmt->fetchAll();

// Get completed events
$stmt = $db->query("SELECT * FROM event_reservations WHERE status = 'completed' ORDER BY updated_at DESC");
$completed_events = $stmt->fetchAll();

// Calculate analytics
$total_revenue = array_sum(array_column($completed_bookings, 'total_cost'));
$total_bookings = count($completed_bookings);
$total_dining = count($completed_dining);
$total_events = count($completed_events);

// Calculate dining revenue (estimated)
$dining_revenue = 0;
foreach ($completed_dining as $dining) {
    $price_per_guest = ['la-vista' => 50, 'cafe-serenity' => 40, 'sky-lounge' => 60];
    $dining_revenue += ($price_per_guest[$dining['restaurant']] ?? 50) * $dining['guests'];
}

// Room type analysis
$room_stats = [];
foreach ($completed_bookings as $booking) {
    $room_stats[$booking['room_type']] = ($room_stats[$booking['room_type']] ?? 0) + 1;
}

// Restaurant analysis
$restaurant_stats = [];
foreach ($completed_dining as $dining) {
    $restaurant_stats[$dining['restaurant']] = ($restaurant_stats[$dining['restaurant']] ?? 0) + 1;
}

// Event type analysis
$event_stats = [];
foreach ($completed_events as $event) {
    $event_stats[$event['event_type']] = ($event_stats[$event['event_type']] ?? 0) + 1;
}

// Monthly completion trends
$monthly_stats = [];
foreach ($completed_bookings as $booking) {
    $month = date('M Y', strtotime($booking['updated_at']));
    $monthly_stats[$month]['bookings'] = ($monthly_stats[$month]['bookings'] ?? 0) + 1;
}
foreach ($completed_dining as $dining) {
    $month = date('M Y', strtotime($dining['updated_at']));
    $monthly_stats[$month]['dining'] = ($monthly_stats[$month]['dining'] ?? 0) + 1;
}
foreach ($completed_events as $event) {
    $month = date('M Y', strtotime($event['updated_at']));
    $monthly_stats[$month]['events'] = ($monthly_stats[$month]['events'] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - DULUX</title>
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
        }
        
        .admin-nav {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 1rem;
        }
        
        .admin-nav a {
            color: #333;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: #e74c3c;
            color: white;
        }
        
        .section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-container {
            height: 300px;
            margin-top: 1rem;
        }
        
        .completed-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .completed-table th,
        .completed-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .completed-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .revenue-highlight {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .revenue-highlight h2 {
            font-size: 3rem;
            margin: 0;
        }
        
        .download-btn {
            background: white;
            color: #e74c3c;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .download-btn:hover {
            background: #e74c3c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
                    <p>Analyze completed tasks and system performance</p>
                </div>
                <a href="download_report.php" class="download-btn">
                    <i class="fas fa-download"></i> Download Report
                </a>
            </div>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="bookings.php"><i class="fas fa-bed"></i> Bookings</a>
            <a href="dining.php"><i class="fas fa-utensils"></i> Dining</a>
            <a href="events.php"><i class="fas fa-glass-cheers"></i> Events</a>
            <a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="../logout.php" style="margin-left: auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Revenue Highlight -->
        <div class="revenue-highlight">
            <h2>$<?php echo number_format($total_revenue + $dining_revenue, 2); ?></h2>
            <p>Total Revenue (Bookings: $<?php echo number_format($total_revenue, 2); ?> + Dining: $<?php echo number_format($dining_revenue, 2); ?>)</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-bed" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3><?php echo $total_bookings; ?></h3>
                <p>Completed Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-utensils" style="font-size: 2rem; color: #28a745;"></i>
                <h3><?php echo $total_dining; ?></h3>
                <p>Completed Dining</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-glass-cheers" style="font-size: 2rem; color: #17a2b8;"></i>
                <h3><?php echo $total_events; ?></h3>
                <p>Completed Events</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line" style="font-size: 2rem; color: #ffc107;"></i>
                <h3><?php echo $total_bookings + $total_dining + $total_events; ?></h3>
                <p>Total Completed</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign" style="font-size: 2rem; color: #6f42c1;"></i>
                <h3>$<?php echo number_format(($total_revenue + $dining_revenue) / max(1, $total_bookings + $total_dining), 2); ?></h3>
                <p>Avg Revenue per Task</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="reports-grid">
            <div class="section">
                <h2><i class="fas fa-chart-pie"></i> Room Type Analysis</h2>
                <div class="chart-container">
                    <canvas id="roomChart"></canvas>
                </div>
            </div>
            <div class="section">
                <h2><i class="fas fa-chart-doughnut"></i> Restaurant Analysis</h2>
                <div class="chart-container">
                    <canvas id="restaurantChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="reports-grid">
            <div class="section">
                <h2><i class="fas fa-chart-bar"></i> Event Type Analysis</h2>
                <div class="chart-container">
                    <canvas id="eventChart"></canvas>
                </div>
            </div>
            <div class="section">
                <h2><i class="fas fa-chart-line"></i> Monthly Completion Trends</h2>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Completed Bookings -->
        <div class="section">
            <h2><i class="fas fa-check-circle"></i> Recent Completed Bookings</h2>
            <table class="completed-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room Type</th>
                        <th>Dates</th>
                        <th>Revenue</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($completed_bookings, 0, 10) as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                            <td><?php echo ucfirst($booking['room_type']); ?></td>
                            <td><?php echo date('M j', strtotime($booking['check_in_date'])) . ' - ' . date('M j, Y', strtotime($booking['check_out_date'])); ?></td>
                            <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($booking['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Completed Dining -->
        <div class="section">
            <h2><i class="fas fa-utensils"></i> Recent Completed Dining</h2>
            <table class="completed-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Restaurant</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Revenue</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($completed_dining, 0, 10) as $dining): 
                        $price_per_guest = ['la-vista' => 50, 'cafe-serenity' => 40, 'sky-lounge' => 60];
                        $revenue = ($price_per_guest[$dining['restaurant']] ?? 50) * $dining['guests'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dining['guest_name']); ?></td>
                            <td><?php echo ucfirst(str_replace('-', ' ', $dining['restaurant'])); ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($dining['reservation_date'] . ' ' . $dining['reservation_time'])); ?></td>
                            <td><?php echo $dining['guests']; ?></td>
                            <td>$<?php echo number_format($revenue, 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($dining['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Completed Events -->
        <div class="section">
            <h2><i class="fas fa-glass-cheers"></i> Recent Completed Events</h2>
            <table class="completed-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Event Type</th>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Guests</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($completed_events, 0, 10) as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['guest_name']); ?></td>
                            <td><?php echo ucfirst($event['event_type']); ?></td>
                            <td><?php echo ucfirst(str_replace('-', ' ', $event['venue'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($event['event_date'])); ?></td>
                            <td><?php echo $event['guests_range']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($event['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Room Type Chart
        const roomCtx = document.getElementById('roomChart').getContext('2d');
        new Chart(roomCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($room_stats)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($room_stats)); ?>,
                    backgroundColor: ['#e74c3c', '#3498db', '#f39c12']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Restaurant Chart
        const restaurantCtx = document.getElementById('restaurantChart').getContext('2d');
        new Chart(restaurantCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_map(function($r) { return ucfirst(str_replace('-', ' ', $r)); }, array_keys($restaurant_stats))); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($restaurant_stats)); ?>,
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Event Type Chart
        const eventCtx = document.getElementById('eventChart').getContext('2d');
        new Chart(eventCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map('ucfirst', array_keys($event_stats))); ?>,
                datasets: [{
                    label: 'Completed Events',
                    data: <?php echo json_encode(array_values($event_stats)); ?>,
                    backgroundColor: '#6f42c1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Monthly Trends Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($monthly_stats)); ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?php echo json_encode(array_column($monthly_stats, 'bookings')); ?>,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)'
                }, {
                    label: 'Dining',
                    data: <?php echo json_encode(array_column($monthly_stats, 'dining')); ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)'
                }, {
                    label: 'Events',
                    data: <?php echo json_encode(array_column($monthly_stats, 'events')); ?>,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
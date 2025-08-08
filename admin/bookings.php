<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$db = getDB();
$message = '';
$message_type = '';

// Handle status updates and deletions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $booking_id = (int)$_POST['booking_id'];
        $new_status = sanitize($_POST['status']);
        
        if (in_array($new_status, ['pending', 'confirmed', 'cancelled', 'completed'])) {
            try {
                // Update booking status
                $stmt = $db->prepare("UPDATE room_bookings SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $booking_id]);
                
                $message = "Status updated successfully!";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = "Error updating status: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    if (isset($_POST['delete_booking'])) {
        $booking_id = (int)$_POST['booking_id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM room_bookings WHERE id = ?");
            $stmt->execute([$booking_id]);
            
            $message = "Booking deleted successfully!";
            $message_type = 'success';
        } catch (Exception $e) {
            $message = "Error deleting booking: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Get all bookings
$stmt = $db->query("SELECT * FROM room_bookings ORDER BY created_at DESC");
$bookings = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM room_bookings");
$total_bookings = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM room_bookings WHERE status = 'pending'");
$pending_bookings = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM room_bookings WHERE status = 'confirmed'");
$confirmed_bookings = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - DULUX</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .bookings-table th,
        .bookings-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .bookings-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .bookings-table tr:hover {
            background: #f8f9fa;
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
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
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
        
        select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 0.5rem;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1><i class="fas fa-bed"></i> Bookings Management</h1>
            <p>Manage all room bookings for DULUX</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-bed" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock" style="font-size: 2rem; color: #f39c12;"></i>
                <h3><?php echo $pending_bookings; ?></h3>
                <p>Pending Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #27ae60;"></i>
                <h3><?php echo $confirmed_bookings; ?></h3>
                <p>Confirmed Bookings</p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="bookings.php" class="active"><i class="fas fa-bed"></i> Bookings</a>
            <a href="dining.php"><i class="fas fa-utensils"></i> Dining</a>
            <a href="events.php"><i class="fas fa-glass-cheers"></i> Events</a>
            <a href="../logout.php" style="margin-left: auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Bookings Table -->
        <div class="section">
            <h2><i class="fas fa-list"></i> All Room Bookings</h2>
            
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Room & Package</th>
                        <th>Check-in/out</th>
                        <th>Guests</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['guest_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($booking['email']); ?></small><br>
                                <small><?php echo htmlspecialchars($booking['booking_reference']); ?></small>
                            </td>
                            <td>
                                <?php echo ucfirst($booking['room_type']); ?> Room<br>
                                <small><?php echo ucfirst($booking['package_type']); ?> Package</small>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?><br>
                                <small>to <?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></small>
                            </td>
                            <td><?php echo $booking['guests']; ?></td>
                            <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $booking['status']; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="delete_booking" class="delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
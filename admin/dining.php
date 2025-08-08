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
        $reservation_id = (int)$_POST['reservation_id'];
        $new_status = sanitize($_POST['status']);
        
        if (in_array($new_status, ['pending', 'confirmed', 'cancelled', 'completed'])) {
            try {
                // Update reservation status
                $stmt = $db->prepare("UPDATE dining_reservations SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $reservation_id]);
                
                $message = "Status updated successfully!";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = "Error updating status: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    if (isset($_POST['delete_reservation'])) {
        $reservation_id = (int)$_POST['reservation_id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM dining_reservations WHERE id = ?");
            $stmt->execute([$reservation_id]);
            
            $message = "Reservation deleted successfully!";
            $message_type = 'success';
        } catch (Exception $e) {
            $message = "Error deleting reservation: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Get all dining reservations
$stmt = $db->query("SELECT * FROM dining_reservations ORDER BY created_at DESC");
$dining_reservations = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM dining_reservations");
$total_reservations = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM dining_reservations WHERE status = 'pending'");
$pending_reservations = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM dining_reservations WHERE status = 'confirmed'");
$confirmed_reservations = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dining Management - DULUX</title>
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
        
        .reservations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .reservations-table th,
        .reservations-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .reservations-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .reservations-table tr:hover {
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
            <h1><i class="fas fa-utensils"></i> Dining Management</h1>
            <p>Manage all dining reservations for DULUX</p>
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
                <i class="fas fa-utensils" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3><?php echo $total_reservations; ?></h3>
                <p>Total Reservations</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock" style="font-size: 2rem; color: #f39c12;"></i>
                <h3><?php echo $pending_reservations; ?></h3>
                <p>Pending Reservations</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #27ae60;"></i>
                <h3><?php echo $confirmed_reservations; ?></h3>
                <p>Confirmed Reservations</p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="bookings.php"><i class="fas fa-bed"></i> Bookings</a>
            <a href="dining.php" class="active"><i class="fas fa-utensils"></i> Dining</a>
            <a href="events.php"><i class="fas fa-glass-cheers"></i> Events</a>
            <a href="../logout.php" style="margin-left: auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Dining Reservations Table -->
        <div class="section">
            <h2><i class="fas fa-list"></i> All Dining Reservations</h2>
            
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Restaurant</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dining_reservations as $reservation): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($reservation['guest_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($reservation['email']); ?></small>
                            </td>
                            <td><?php echo ucfirst(str_replace('-', ' ', $reservation['restaurant'])); ?></td>
                            <td>
                                <?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?><br>
                                <small><?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></small>
                            </td>
                            <td><?php echo $reservation['guests']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                    <?php echo ucfirst($reservation['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $reservation['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $reservation['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $reservation['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        <option value="completed" <?php echo $reservation['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this reservation?')">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                    <button type="submit" name="delete_reservation" class="delete-btn">
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
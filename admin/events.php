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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add_event') {
        $guest_name = sanitize($_POST['guest_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $event_type = sanitize($_POST['event_type'] ?? '');
        $event_date = $_POST['event_date'] ?? '';
        $venue = sanitize($_POST['venue'] ?? '');
        $status = sanitize($_POST['status'] ?? 'pending');
        
        if (empty($guest_name) || empty($email) || empty($event_type) || empty($event_date) || empty($venue)) {
            $message = "Required fields must be filled!";
            $message_type = 'error';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO event_reservations (guest_name, email, event_type, event_date, venue, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$guest_name, $email, $event_type, $event_date, $venue, $status]);
                
                $message = "Event reservation created successfully!";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = "Error creating event reservation: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif ($action == 'delete_event') {
        $event_id = (int)($_POST['event_id'] ?? 0);
        
        if ($event_id > 0) {
            try {
                $stmt = $db->prepare("DELETE FROM event_reservations WHERE id = ?");
                $stmt->execute([$event_id]);
                
                if ($stmt->rowCount() > 0) {
                    $message = "Event reservation deleted successfully!";
                    $message_type = 'success';
                } else {
                    $message = "Event reservation not found!";
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                $message = "Error deleting event reservation: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif ($action == 'update_status') {
        $event_id = (int)($_POST['event_id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');
        
        if ($event_id > 0 && !empty($status)) {
            try {
                $stmt = $db->prepare("UPDATE event_reservations SET status = ? WHERE id = ?");
                $stmt->execute([$status, $event_id]);

                // If status is completed, insert into event_reports
                if ($status === 'completed') {
                    $stmt2 = $db->prepare("SELECT * FROM event_reservations WHERE id = ?");
                    $stmt2->execute([$event_id]);
                    $event = $stmt2->fetch();
                    if ($event) {
                        // Use dynamic pricing if available
                        require_once 'pricing.php';
                        $event_type = $event['event_type'];
                        $price = 200; // Example: $200 per event, replace with dynamic pricing if available
                        $stmt3 = $db->prepare("INSERT INTO event_reports (event_id, guest_name, email, event_type, event_date, venue, total_cost, completed_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                        $stmt3->execute([
                            $event['id'],
                            $event['guest_name'],
                            $event['email'],
                            $event_type,
                            $event['event_date'],
                            $event['venue'],
                            $price
                        ]);
                    }
                }

                $message = "Status updated successfully!";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = "Error updating status: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Get all event reservations
$stmt = $db->query("SELECT * FROM event_reservations ORDER BY created_at DESC");
$event_reservations = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM event_reservations");
$total_events = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM event_reservations WHERE status = 'pending'");
$pending_events = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM event_reservations WHERE status = 'confirmed'");
$confirmed_events = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - DULUX</title>
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
        }
        
        .admin-nav a {
            color: #333;
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: #e74c3c;
            color: white;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
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
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #e74c3c;
        }
        
        .btn {
            background: #e74c3c;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .events-table th,
        .events-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .events-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .events-table tr:hover {
            background: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
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
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1><i class="fas fa-glass-cheers"></i> Events Management</h1>
            <p>Manage all event reservations for DULUX</p>
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
                <i class="fas fa-glass-cheers" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3><?php echo $total_events; ?></h3>
                <p>Total Events</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock" style="font-size: 2rem; color: #f39c12;"></i>
                <h3><?php echo $pending_events; ?></h3>
                <p>Pending Events</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #27ae60;"></i>
                <h3><?php echo $confirmed_events; ?></h3>
                <p>Confirmed Events</p>
            </div>
        </div>

        <!-- Admin Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="bookings.php"><i class="fas fa-bed"></i> Bookings</a>
            <a href="dining.php"><i class="fas fa-utensils"></i> Dining</a>
            <a href="events.php" class="active"><i class="fas fa-glass-cheers"></i> Events</a>
            <a href="pricing.php"><i class="fas fa-dollar-sign"></i> Pricing</a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="../logout.php" style="float: right;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Add Event Form -->
            <div class="section">
                <h2><i class="fas fa-plus"></i> Add New Event</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_event">
                    
                    <div class="form-group">
                        <label for="guest_name">Guest Name *</label>
                        <input type="text" id="guest_name" name="guest_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_type">Event Type *</label>
                        <select id="event_type" name="event_type" required>
                            <option value="">Select Event Type</option>
                            <option value="wedding">Wedding</option>
                            <option value="corporate">Corporate</option>
                            <option value="conference">Conference</option>
                            <option value="birthday">Birthday</option>
                            <option value="anniversary">Anniversary</option>
                            <option value="graduation">Graduation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_date">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="venue">Venue *</label>
                        <select id="venue" name="venue" required>
                            <option value="">Select Venue</option>
                            <option value="grand-ballroom">Grand Ballroom</option>
                            <option value="conference-center">Conference Center</option>
                            <option value="garden-terrace">Garden Terrace</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-plus"></i> Add Event
                    </button>
                </form>
            </div>

            <!-- Events List -->
            <div class="section">
                <h2><i class="fas fa-list"></i> All Events (<?php echo count($event_reservations); ?>)</h2>
                
                <?php if (empty($event_reservations)): ?>
                    <p>No event reservations found.</p>
                <?php else: ?>
                    <table class="events-table">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Event Type</th>
                                <th>Date</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($event_reservations as $event): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($event['guest_name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($event['email']); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo ucfirst(htmlspecialchars($event['event_type'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo ucfirst(str_replace('-', ' ', $event['venue'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $event['status']; ?>">
                                            <?php echo ucfirst($event['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-small" onclick="updateStatus(<?php echo $event['id']; ?>, '<?php echo $event['status']; ?>')">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <button class="btn btn-small btn-secondary" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <p>Are you sure you want to delete this event reservation? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete_event">
                <input type="hidden" name="event_id" id="delete_event_id">
                
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-trash"></i> Delete Event
                </button>
                <button type="button" class="btn" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-sync"></i> Update Event Status</h2>
            <form method="POST" id="statusForm">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="event_id" id="status_event_id">
                
                <div class="form-group">
                    <label for="status_select">New Status</label>
                    <select id="status_select" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update Status
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const deleteModal = document.getElementById('deleteModal');
        const statusModal = document.getElementById('statusModal');

        window.onclick = function(event) {
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
            if (event.target == statusModal) {
                statusModal.style.display = 'none';
            }
        }

        function deleteEvent(eventId) {
            document.getElementById('delete_event_id').value = eventId;
            deleteModal.style.display = 'block';
        }

        function updateStatus(eventId, currentStatus) {
            document.getElementById('status_event_id').value = eventId;
            document.getElementById('status_select').value = currentStatus;
            statusModal.style.display = 'block';
        }

        function closeDeleteModal() {
            deleteModal.style.display = 'none';
        }

        function closeStatusModal() {
            statusModal.style.display = 'none';
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html> 
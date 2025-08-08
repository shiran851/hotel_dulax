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
    
    if ($action == 'add_user') {
        $firstname = sanitize($_POST['firstname'] ?? '');
        $lastname = sanitize($_POST['lastname'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate required fields
        if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            $message = "All fields are required!";
            $message_type = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address!";
            $message_type = 'error';
        } else {
            try {
                // Check if email already exists
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $message = "Email already exists!";
                    $message_type = 'error';
                } else {
                    // Create new user
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (firstname, lastname, email, phone, country, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$firstname, $lastname, $email, $phone, $country, $password_hash]);
                    
                    $message = "User added successfully!";
                    $message_type = 'success';
                }
            } catch (Exception $e) {
                $message = "Error adding user: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif ($action == 'delete_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        
        if ($user_id > 0) {
            try {
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                
                if ($stmt->rowCount() > 0) {
                    $message = "User deleted successfully!";
                    $message_type = 'success';
                } else {
                    $message = "User not found!";
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                $message = "Error deleting user: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif ($action == 'update_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $firstname = sanitize($_POST['firstname'] ?? '');
        $lastname = sanitize($_POST['lastname'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($user_id > 0 && !empty($firstname) && !empty($lastname) && !empty($email)) {
            try {
                // Check if email exists for other users
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $user_id]);
                if ($stmt->fetch()) {
                    $message = "Email already exists!";
                    $message_type = 'error';
                } else {
                    if (!empty($password)) {
                        // Update with new password
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ?, country = ?, password_hash = ? WHERE id = ?");
                        $stmt->execute([$firstname, $lastname, $email, $phone, $country, $password_hash, $user_id]);
                    } else {
                        // Update without password
                        $stmt = $db->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ?, country = ? WHERE id = ?");
                        $stmt->execute([$firstname, $lastname, $email, $phone, $country, $user_id]);
                    }
                    
                    $message = "User updated successfully!";
                    $message_type = 'success';
                }
            } catch (Exception $e) {
                $message = "Error updating user: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Get all users
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Get user statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()");
$new_users_today = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$new_users_week = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - DULUX </title>
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
            grid-template-columns: 1fr 1fr;
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
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .users-table tr:hover {
            background: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
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
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1><i class="fas fa-users"></i> User Management</h1>
            <p>Manage all user accounts for DULUX</p>
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
                <i class="fas fa-users" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3><?php echo $total_users; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-plus" style="font-size: 2rem; color: #27ae60;"></i>
                <h3><?php echo $new_users_today; ?></h3>
                <p>New Users Today</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line" style="font-size: 2rem; color: #3498db;"></i>
                <h3><?php echo $new_users_week; ?></h3>
                <p>New Users This Week</p>
            </div>
        </div>

        <!-- Admin Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php" class="active"><i class="fas fa-users"></i> Users</a>
            <a href="pricing.php"><i class="fas fa-dollar-sign"></i> Pricing</a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="../logout.php" style="float: right;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Add User Form -->
            <div class="section">
                <h2><i class="fas fa-user-plus"></i> Add New User</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_user">
                    
                    <div class="form-group">
                        <label for="firstname">First Name *</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastname">Last Name *</label>
                        <input type="text" id="lastname" name="lastname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="CA">Canada</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                            <option value="IN">India</option>
                            <option value="BR">Brazil</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </form>
            </div>

            <!-- Users List -->
            <div class="section">
                <h2><i class="fas fa-list"></i> All Users (<?php echo count($users); ?>)</h2>
                
                <?php if (empty($users)): ?>
                    <p>No users found.</p>
                <?php else: ?>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($user['country'] ?? '-'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-small" onclick="editUser(<?php echo $user['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-small btn-secondary" onclick="deleteUser(<?php echo $user['id']; ?>)">
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

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit User</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="edit_user_id">
                
                <div class="form-group">
                    <label for="edit_firstname">First Name *</label>
                    <input type="text" id="edit_firstname" name="firstname" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_lastname">Last Name *</label>
                    <input type="text" id="edit_lastname" name="lastname" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email Address *</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_phone">Phone Number</label>
                    <input type="tel" id="edit_phone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="edit_country">Country</label>
                    <select id="edit_country" name="country">
                        <option value="">Select Country</option>
                        <option value="US">United States</option>
                        <option value="UK">United Kingdom</option>
                        <option value="CA">Canada</option>
                        <option value="AU">Australia</option>
                        <option value="DE">Germany</option>
                        <option value="FR">France</option>
                        <option value="JP">Japan</option>
                        <option value="IN">India</option>
                        <option value="BR">Brazil</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">New Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password">
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update User
                </button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" id="delete_user_id">
                
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-trash"></i> Delete User
                </button>
                <button type="button" class="btn" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const editModal = document.getElementById('editModal');
        const deleteModal = document.getElementById('deleteModal');
        const closeBtn = document.querySelector('.close');

        closeBtn.onclick = function() {
            editModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }

        function editUser(userId) {
            // Fetch user data via AJAX
            fetch('get_user.php?user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }
                    
                    // Populate the form fields
                    document.getElementById('edit_user_id').value = data.id;
                    document.getElementById('edit_firstname').value = data.firstname;
                    document.getElementById('edit_lastname').value = data.lastname;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_phone').value = data.phone || '';
                    document.getElementById('edit_country').value = data.country || '';
                    
                    // Show the modal
                    editModal.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching user data');
                });
        }

        function deleteUser(userId) {
            document.getElementById('delete_user_id').value = userId;
            deleteModal.style.display = 'block';
        }

        function closeDeleteModal() {
            deleteModal.style.display = 'none';
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
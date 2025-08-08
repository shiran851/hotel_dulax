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
$message = '';
$message_type = '';

// Handle pricing updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pricing'])) {
    $room_deluxe = (float)$_POST['room_deluxe'];
    $room_suite = (float)$_POST['room_suite'];
    $room_presidential = (float)$_POST['room_presidential'];
    
    $package_individual = (float)$_POST['package_individual'];
    $package_couple = (float)$_POST['package_couple'];
    $package_family = (float)$_POST['package_family'];
    
    $dining_la_vista = (float)$_POST['dining_la_vista'];
    $dining_cafe_serenity = (float)$_POST['dining_cafe_serenity'];
    $dining_sky_lounge = (float)$_POST['dining_sky_lounge'];
    
    $message = "Pricing updated successfully! (Note: This is a demo - prices are hardcoded in the system)";
    $message_type = 'success';
}

// Default pricing (hardcoded in system)
$pricing = [
    'rooms' => ['deluxe' => 150, 'suite' => 250, 'presidential' => 500],
    'packages' => ['individual' => 120, 'couple' => 200, 'family' => 300],
    'dining' => ['la-vista' => 50, 'cafe-serenity' => 40, 'sky-lounge' => 60]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Management - DULUX</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
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
        
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .pricing-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .pricing-section h3 {
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
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .btn {
            background: #e74c3c;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-dollar-sign"></i> Pricing Management</h1>
            <p>Manage room, package, and dining prices</p>
        </div>

        <!-- Navigation -->
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="bookings.php"><i class="fas fa-bed"></i> Bookings</a>
            <a href="dining.php"><i class="fas fa-utensils"></i> Dining</a>
            <a href="events.php"><i class="fas fa-glass-cheers"></i> Events</a>
            <a href="pricing.php" class="active"><i class="fas fa-dollar-sign"></i> Pricing</a>
            <a href="../logout.php" style="margin-left: auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="pricing-grid">
                <!-- Room Pricing -->
                <div class="pricing-section">
                    <h3><i class="fas fa-bed"></i> Room Pricing</h3>
                    <div class="form-group">
                        <label>Deluxe Room (per night)</label>
                        <input type="number" name="room_deluxe" value="<?php echo $pricing['rooms']['deluxe']; ?>" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Executive Suite (per night)</label>
                        <input type="number" name="room_suite" value="<?php echo $pricing['rooms']['suite']; ?>" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Presidential Suite (per night)</label>
                        <input type="number" name="room_presidential" value="<?php echo $pricing['rooms']['presidential']; ?>" step="0.01">
                    </div>
                </div>

                <!-- Package Pricing -->
                <div class="pricing-section">
                    <h3><i class="fas fa-gift"></i> Package Pricing</h3>
                    <div class="form-group">
                        <label>Individual Package (per night)</label>
                        <input type="number" name="package_individual" value="<?php echo $pricing['packages']['individual']; ?>" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Couple Package (per night)</label>
                        <input type="number" name="package_couple" value="<?php echo $pricing['packages']['couple']; ?>" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Family Package (per night)</label>
                        <input type="number" name="package_family" value="<?php echo $pricing['packages']['family']; ?>" step="0.01">
                    </div>
                </div>

                <!-- Dining Pricing -->
                <div class="pricing-section">
                    <h3><i class="fas fa-utensils"></i> Dining Pricing</h3>
                    <div class="form-group">
                        <label>La Vista (per guest)</label>
                        <input type="number" name="dining_la_vista" value="<?php echo $pricing['dining']['la-vista']; ?>" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Cafe Serenity (per guest)</label>
                        <input type="number" name="dining_cafe_serenity" value="<?php echo $pricing['dining']['cafe-serenity']; ?>" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Sky Lounge (per guest)</label>
                        <input type="number" name="dining_sky_lounge" value="<?php echo $pricing['dining']['sky-lounge']; ?>" step="0.01">
                    </div>
                </div>
            </div>

            <button type="submit" name="update_pricing" class="btn">
                <i class="fas fa-save"></i> Update Pricing
            </button>
        </form>
    </div>
</body>
</html>
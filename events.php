<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Reservation -DULUX </title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .events-page {
            padding-top: 100px;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .events-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }
        
        .events-header h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .events-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .events-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .venue-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .venue-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .venue-card:hover {
            transform: translateY(-5px);
        }
        
        .venue-card i {
            font-size: 3rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .venue-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .venue-card p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .venue-card .capacity {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #e74c3c;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 2rem;
            transition: transform 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        
        .back-btn {
            position: fixed;
            top: 120px;
            left: 2rem;
            background: white;
            color: #2c3e50;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .service-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .service-item:hover {
            border-color: #e74c3c;
            background: white;
        }
        
        .service-item input[type="checkbox"] {
            width: auto;
            margin-right: 0.5rem;
        }
        
        /* Alert Styles */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
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
        
        @media (max-width: 768px) {
            .events-header h1 {
                font-size: 2.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .venue-info {
                grid-template-columns: 1fr;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
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
                <li><a href="index.php#packages" class="nav-link">Packages</a></li>
                <li><a href="index.php#amenities" class="nav-link">Amenities</a></li>
                <li><a href="index.php#contact" class="nav-link">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link">Reservations <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="booking.php"><i class="fas fa-bed"></i> Room Booking</a></li>
                        <li><a href="dining.php"><i class="fas fa-utensils"></i> Dining Reservation</a></li>
                        <li><a href="events.php"><i class="fas fa-glass-cheers"></i> Event Reservation</a></li>
                    </ul>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="dropdown">
                        <a href="#" class="nav-link">My Account <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="my_bookings.php"><i class="fas fa-list"></i> My Bookings</a></li>
                            <li><a href="my_dining.php"><i class="fas fa-utensils"></i> My Dining</a></li>
                            <li><a href="my_events.php"><i class="fas fa-glass-cheers"></i> My Events</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                    <li><a href="register.php" class="nav-link">Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Back Button -->
    <a href="index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <!-- Events Page -->
    <section class="events-page">
        <div class="events-container">
            <div class="events-header">
                <h1>Host Your Perfect Event</h1>
                <p>Create unforgettable memories in our stunning venues</p>
            </div>
            
            <div class="events-content">
                <?php
                // Display success or error messages
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message'] ?? 'Event request submitted successfully!') . '</div>';
                }
                if (isset($_GET['error']) && $_GET['error'] == '1') {
                    echo '<div class="alert alert-error">' . htmlspecialchars($_GET['message'] ?? 'An error occurred. Please try again.') . '</div>';
                }
                ?>
                <!-- Venue Information -->
                <div class="venue-info">
                    <div class="venue-card">
                        <i class="fas fa-glass-cheers"></i>
                        <h3>Grand Ballroom</h3>
                        <p>Elegant space for large events and celebrations</p>
                        <span class="capacity">Up to 500 guests</span>
                    </div>
                    <div class="venue-card">
                        <i class="fas fa-users"></i>
                        <h3>Conference Center</h3>
                        <p>Professional setting for business meetings</p>
                        <span class="capacity">Up to 200 guests</span>
                    </div>
                    <div class="venue-card">
                        <i class="fas fa-heart"></i>
                        <h3>Garden Terrace</h3>
                        <p>Intimate outdoor venue for special occasions</p>
                        <span class="capacity">Up to 100 guests</span>
                    </div>
                </div>

                <form class="events-form" id="eventsForm" action="process_events.php" method="POST">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Contact Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="company">Company/Organization</label>
                                <input type="text" id="company" name="company">
                            </div>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="form-section">
                        <h3><i class="fas fa-calendar-alt"></i> Event Details</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="event-type">Event Type *</label>
                                <select id="event-type" name="event-type" required>
                                    <option value="">Select Event Type</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="corporate">Corporate Event</option>
                                    <option value="conference">Conference</option>
                                    <option value="birthday">Birthday Party</option>
                                    <option value="anniversary">Anniversary</option>
                                    <option value="graduation">Graduation</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="guests">Expected Number of Guests *</label>
                                <select id="guests" name="guests" required>
                                    <option value="">Select Guest Count</option>
                                    <option value="1-50">1-50 guests</option>
                                    <option value="51-100">51-100 guests</option>
                                    <option value="101-200">101-200 guests</option>
                                    <option value="201-500">201-500 guests</option>
                                    <option value="500+">500+ guests</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="date">Event Date *</label>
                                <input type="date" id="date" name="date" required>
                            </div>
                            <div class="form-group">
                                <label for="time">Event Time *</label>
                                <select id="time" name="time" required>
                                    <option value="">Select Time</option>
                                    <option value="morning">Morning (8:00 AM - 12:00 PM)</option>
                                    <option value="afternoon">Afternoon (12:00 PM - 4:00 PM)</option>
                                    <option value="evening">Evening (4:00 PM - 8:00 PM)</option>
                                    <option value="night">Night (8:00 PM - 12:00 AM)</option>
                                    <option value="full-day">Full Day</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Venue Selection -->
                    <div class="form-section">
                        <h3><i class="fas fa-building"></i> Choose Your Venue</h3>
                        <div class="form-group">
                            <label for="venue">Preferred Venue *</label>
                            <select id="venue" name="venue" required>
                                <option value="">Select Venue</option>
                                <option value="grand-ballroom">Grand Ballroom - Up to 500 guests</option>
                                <option value="conference-center">Conference Center - Up to 200 guests</option>
                                <option value="garden-terrace">Garden Terrace - Up to 100 guests</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Services -->
                    <div class="form-section">
                        <h3><i class="fas fa-plus-circle"></i> Additional Services</h3>
                        <div class="services-grid">
                            <div class="service-item">
                                <input type="checkbox" id="catering" name="services[]" value="catering">
                                <label for="catering">Catering Service</label>
                            </div>
                            <div class="service-item">
                                <input type="checkbox" id="decoration" name="services[]" value="decoration">
                                <label for="decoration">Decoration & Setup</label>
                            </div>
                            <div class="service-item">
                                <input type="checkbox" id="audio-visual" name="services[]" value="audio-visual">
                                <label for="audio-visual">Audio/Visual Equipment</label>
                            </div>
                            <div class="service-item">
                                <input type="checkbox" id="photography" name="services[]" value="photography">
                                <label for="photography">Photography/Videography</label>
                            </div>
                            <div class="service-item">
                                <input type="checkbox" id="transportation" name="services[]" value="transportation">
                                <label for="transportation">Transportation</label>
                            </div>
                            <div class="service-item">
                                <input type="checkbox" id="accommodation" name="services[]" value="accommodation">
                                <label for="accommodation">Guest Accommodation</label>
                            </div>
                        </div>
                    </div>

                    <!-- Budget -->
                    <div class="form-section">
                        <h3><i class="fas fa-dollar-sign"></i> Budget Range</h3>
                        <div class="form-group">
                            <label for="budget">What's your budget range?</label>
                            <select id="budget" name="budget">
                                <option value="">Select Budget Range</option>
                                <option value="under-5000">Under $5,000</option>
                                <option value="5000-10000">$5,000 - $10,000</option>
                                <option value="10000-25000">$10,000 - $25,000</option>
                                <option value="25000-50000">$25,000 - $50,000</option>
                                <option value="50000+">$50,000+</option>
                            </select>
                        </div>
                    </div>

                    <!-- Special Requirements -->
                    <div class="form-section">
                        <h3><i class="fas fa-comment"></i> Special Requirements</h3>
                        <div class="form-group">
                            <label for="requirements">Any special requirements or requests?</label>
                            <textarea id="requirements" name="requirements" rows="4" placeholder="Tell us about any special requirements, themes, or specific needs for your event..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-check"></i> Submit Event Request
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script src="js/script.js"></script>
</body>
</html> 
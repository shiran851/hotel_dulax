<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel DULUX - Luxury Accommodations</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#home" class="nav-logo">
                <div class="logo-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="logo-text">
                    <h2>DULUX</h2>
                    <span>Luxury Hotel</span>
                </div>
            </a>
            <ul class="nav-menu">
                <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#rooms" class="nav-link">Rooms</a></li>
                <li><a href="#packages" class="nav-link">Packages</a></li>
                <li><a href="#amenities" class="nav-link">Amenities</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
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

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Experience Luxury Like Never Before</h1>
            <p class="hero-subtitle">Discover comfort, elegance, and exceptional service at DULUX</p>
            <div class="hero-buttons">
                <a href="booking.php" class="btn btn-primary">Room Booking</a>
                <a href="dining.php" class="btn btn-secondary">Dining Reservation</a>
                <a href="events.php" class="btn btn-secondary">Event Reservation</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="floating-card">
                <div class="card-content">
                    <h3>Special Offer</h3>
                    <p>20% off on Family Packages</p>
                    <span class="offer-badge">Limited Time</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-bed"></i>
                    <h3>Room Booking</h3>
                    <p>Luxury accommodations with world-class amenities</p>
                    <a href="booking.php" class="btn btn-primary">Book Room</a>
                </div>
                <div class="feature-card">
                    <i class="fas fa-utensils"></i>
                    <h3>Dining Reservation</h3>
                    <p>Exquisite cuisine at our award-winning restaurants</p>
                    <a href="dining.php" class="btn btn-primary">Reserve Table</a>
                </div>
                <div class="feature-card">
                    <i class="fas fa-glass-cheers"></i>
                    <h3>Event Reservation</h3>
                    <p>Host unforgettable events in our stunning venues</p>
                    <a href="events.php" class="btn btn-primary">Plan Event</a>
                </div>
                <div class="feature-card">
                    <i class="fas fa-concierge-bell"></i>
                    <h3>24/7 Service</h3>
                    <p>Round-the-clock assistance for all your needs</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="rooms">
        <div class="container">
            <h2 class="section-title">Our Rooms</h2>
            <div class="rooms-grid">
                <div class="room-card">
                    <div class="room-image">
                        <img src="images/deluxe-room.jpg" alt="Deluxe Room">
                        <div class="room-price">$150/night</div>
                    </div>
                    <div class="room-content">
                        <h3>Deluxe Room</h3>
                        <p>Spacious room with city view and modern amenities</p>
                        <ul class="room-features">
                            <li><i class="fas fa-wifi"></i> Free WiFi</li>
                            <li><i class="fas fa-tv"></i> Smart TV</li>
                            <li><i class="fas fa-snowflake"></i> AC</li>
                        </ul>
                        <a href="booking.php?room=deluxe" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="room-card">
                    <div class="room-image">
                        <img src="images/executive-suite.jpg" alt="Suite">
                        <div class="room-price">$250/night</div>
                    </div>
                    <div class="room-content">
                        <h3>Executive Suite</h3>
                        <p>Luxury suite with separate living area and balcony</p>
                        <ul class="room-features">
                            <li><i class="fas fa-wifi"></i> Free WiFi</li>
                            <li><i class="fas fa-tv"></i> Smart TV</li>
                            <li><i class="fas fa-snowflake"></i> AC</li>
                            <li><i class="fas fa-bath"></i> Jacuzzi</li>
                        </ul>
                        <a href="booking.php?room=suite" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="room-card">
                    <div class="room-image">
                        <img src="images/presidential-suite.jpg" alt="Presidential Suite">
                        <div class="room-price">$500/night</div>
                    </div>
                    <div class="room-content">
                        <h3>Presidential Suite</h3>
                        <p>Ultimate luxury with panoramic views and butler service</p>
                        <ul class="room-features">
                            <li><i class="fas fa-wifi"></i> Free WiFi</li>
                            <li><i class="fas fa-tv"></i> Smart TV</li>
                            <li><i class="fas fa-snowflake"></i> AC</li>
                            <li><i class="fas fa-bath"></i> Jacuzzi</li>
                            <li><i class="fas fa-user-tie"></i> Butler</li>
                        </ul>
                        <a href="booking.php?room=presidential" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section id="packages" class="packages">
        <div class="container">
            <h2 class="section-title">Special Packages</h2>
            <div class="packages-grid">
                <div class="package-card family">
                    <div class="package-header">
                        <h3>Family Package</h3>
                        <div class="package-price">$300/night</div>
                    </div>
                    <div class="package-content">
                        <ul>
                            <li>2 Deluxe Rooms</li>
                            <li>Breakfast for 4</li>
                            <li>Kids Club Access</li>
                            <li>Family Pool Access</li>
                            <li>Free Airport Transfer</li>
                        </ul>
                        <a href="booking.php?package=family" class="btn btn-primary">Book Package</a>
                    </div>
                </div>
                <div class="package-card couple">
                    <div class="package-header">
                        <h3>Couple Package</h3>
                        <div class="package-price">$200/night</div>
                    </div>
                    <div class="package-content">
                        <ul>
                            <li>Executive Suite</li>
                            <li>Romantic Dinner</li>
                            <li>Spa Treatment</li>
                            <li>Wine & Champagne</li>
                            <li>Late Checkout</li>
                        </ul>
                        <a href="booking.php?package=couple" class="btn btn-primary">Book Package</a>
                    </div>
                </div>
                <div class="package-card individual">
                    <div class="package-header">
                        <h3>Individual Package</h3>
                        <div class="package-price">$120/night</div>
                    </div>
                    <div class="package-content">
                        <ul>
                            <li>Deluxe Room</li>
                            <li>Breakfast Included</li>
                            <li>Gym Access</li>
                            <li>Business Center</li>
                            <li>Free WiFi</li>
                        </ul>
                        <a href="booking.php?package=individual" class="btn btn-primary">Book Package</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Amenities Section -->
    <section id="amenities" class="amenities">
        <div class="container">
            <h2 class="section-title">Extra Amenities</h2>
            <div class="amenities-grid">
                <div class="amenity-card">
                    <i class="fas fa-swimming-pool"></i>
                    <h3>Swimming Pool</h3>
                    <p>Infinity pool with city views</p>
                    <span class="price">$20/day</span>
                </div>
                <div class="amenity-card">
                    <i class="fas fa-spa"></i>
                    <h3>Spa Treatment</h3>
                    <p>Relaxing massage therapy</p>
                    <span class="price">$80/session</span>
                </div>
                <div class="amenity-card">
                    <i class="fas fa-car"></i>
                    <h3>Airport Transfer</h3>
                    <p>Luxury car service</p>
                    <span class="price">$50/trip</span>
                </div>
                <div class="amenity-card">
                    <i class="fas fa-utensils"></i>
                    <h3>Room Service</h3>
                    <p>24/7 dining in your room</p>
                    <span class="price">$15/delivery</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Address</h3>
                            <p>Flower Road, Colombo 7</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>071 6003002</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>info@DULUX.com</p>
                        </div>
                    </div>
                </div>
                <form class="contact-form">
                    <input type="text" placeholder="Your Name" required>
                    <input type="email" placeholder="Your Email" required>
                    <textarea placeholder="Your Message" rows="5" required></textarea>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>DULUX</h3>
                    <p>Experience luxury and comfort like never before.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#rooms">Rooms</a></li>
                        <li><a href="#packages">Packages</a></li>
                        <li><a href="#amenities">Amenities</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 DULUX. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html> 
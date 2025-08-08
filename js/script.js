// Advanced Navigation and UI Effects
document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Add/remove scrolled class for styling
        if (scrollTop > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Hide/show navbar on scroll (optional)
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Logo animation on hover
    const logo = document.querySelector('.nav-logo');
    if (logo) {
        logo.addEventListener('mouseenter', function() {
            const logoIcon = this.querySelector('.logo-icon');
            if (logoIcon) {
                logoIcon.style.transform = 'rotate(360deg) scale(1.1)';
            }
        });
        
        logo.addEventListener('mouseleave', function() {
            const logoIcon = this.querySelector('.logo-icon');
            if (logoIcon) {
                logoIcon.style.transform = 'rotate(0deg) scale(1)';
            }
        });
    }

    // Dropdown menu enhancements
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        const dropdownLink = dropdown.querySelector('.nav-link');
        
        if (dropdownMenu && dropdownLink) {
            // Add hover delay for better UX
            let timeout;
            
            dropdown.addEventListener('mouseenter', function() {
                clearTimeout(timeout);
                dropdownMenu.style.opacity = '1';
                dropdownMenu.style.visibility = 'visible';
                dropdownMenu.style.transform = 'translateY(0)';
            });
            
            dropdown.addEventListener('mouseleave', function() {
                timeout = setTimeout(() => {
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.visibility = 'hidden';
                    dropdownMenu.style.transform = 'translateY(-10px)';
                }, 150);
            });
        }
    });

    // Form validation and enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Add floating label effect
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.type !== 'checkbox' && input.type !== 'radio') {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            }
        });

        // Form submission with loading state
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;
                
                // Re-enable after a delay (in real app, this would be after form processing)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });

    // Parallax effect for hero section
    const hero = document.querySelector('.hero');
    if (hero) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            hero.style.transform = `translateY(${rate}px)`;
        });
    }

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.feature-card, .room-card, .package-card, .amenity-card').forEach(el => {
        observer.observe(el);
    });

    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
    `;
    
    document.body.appendChild(backToTopBtn);

    // Show/hide back to top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.opacity = '1';
            backToTopBtn.style.visibility = 'visible';
        } else {
            backToTopBtn.style.opacity = '0';
            backToTopBtn.style.visibility = 'hidden';
        }
    });

    // Back to top functionality
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Add hover effect to back to top button
    backToTopBtn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px) scale(1.1)';
    });
    
    backToTopBtn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });

    // Alert auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Add loading animation to page
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            animation: slideInUp 0.6s ease forwards;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }
        
        .logo-icon {
            transition: transform 0.3s ease;
        }
        
        .form-group.focused label {
            color: #e74c3c;
            transform: translateY(-20px) scale(0.8);
        }
        
        body.loaded {
            opacity: 1;
        }
        
        body {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
    `;
    document.head.appendChild(style);
});

// Booking form functionality
class BookingForm {
    constructor() {
        this.initializeForm();
    }

    initializeForm() {
        const form = document.querySelector('.booking-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
            this.setupDatePickers();
            this.setupRoomSelection();
            this.setupPackageSelection();
            this.setupAmenitiesSelection();
        }
    }

    setupDatePickers() {
        const checkIn = document.getElementById('check-in');
        const checkOut = document.getElementById('check-out');
        
        if (checkIn && checkOut) {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            checkIn.min = today;
            
            checkIn.addEventListener('change', () => {
                const checkInDate = new Date(checkIn.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOut.min = nextDay.toISOString().split('T')[0];
                
                if (checkOut.value && new Date(checkOut.value) <= checkInDate) {
                    checkOut.value = nextDay.toISOString().split('T')[0];
                }
            });
        }
    }

    setupRoomSelection() {
        const roomSelect = document.getElementById('room-type');
        const roomPrice = document.getElementById('room-price');
        
        if (roomSelect && roomPrice) {
            const prices = {
                'deluxe': 150,
                'suite': 250,
                'presidential': 500
            };
            
            roomSelect.addEventListener('change', () => {
                const selectedRoom = roomSelect.value;
                if (prices[selectedRoom]) {
                    roomPrice.textContent = `$${prices[selectedRoom]}/night`;
                    this.calculateTotal();
                }
            });
        }
    }

    setupPackageSelection() {
        const packageSelect = document.getElementById('package-type');
        const packagePrice = document.getElementById('package-price');
        
        if (packageSelect && packagePrice) {
            const prices = {
                'family': 300,
                'couple': 200,
                'individual': 120
            };
            
            packageSelect.addEventListener('change', () => {
                const selectedPackage = packageSelect.value;
                if (prices[selectedPackage]) {
                    packagePrice.textContent = `$${prices[selectedPackage]}/night`;
                    this.calculateTotal();
                }
            });
        }
    }

    setupAmenitiesSelection() {
        const amenityCheckboxes = document.querySelectorAll('.amenity-checkbox');
        amenityCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.calculateTotal();
            });
        });
    }

    calculateTotal() {
        const nights = parseInt(document.getElementById('nights').value) || 1;
        const roomType = document.getElementById('room-type').value;
        const packageType = document.getElementById('package-type').value;
        
        let total = 0;
        
        // Room cost
        const roomPrices = { 'deluxe': 150, 'suite': 250, 'presidential': 500 };
        if (roomPrices[roomType]) {
            total += roomPrices[roomType] * nights;
        }
        
        // Package cost
        const packagePrices = { 'family': 300, 'couple': 200, 'individual': 120 };
        if (packagePrices[packageType]) {
            total += packagePrices[packageType] * nights;
        }
        
        // Amenities cost
        const amenityCheckboxes = document.querySelectorAll('.amenity-checkbox:checked');
        amenityCheckboxes.forEach(checkbox => {
            const price = parseInt(checkbox.dataset.price) || 0;
            total += price;
        });
        
        const totalElement = document.getElementById('total-price');
        if (totalElement) {
            totalElement.textContent = `$${total}`;
        }
    }

    handleSubmit(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(e.target);
        const bookingData = Object.fromEntries(formData);
        
        // Add selected amenities
        const selectedAmenities = [];
        document.querySelectorAll('.amenity-checkbox:checked').forEach(checkbox => {
            selectedAmenities.push(checkbox.value);
        });
        bookingData.amenities = selectedAmenities;
        
        // Simulate booking submission
        this.showBookingConfirmation(bookingData);
    }

    showBookingConfirmation(bookingData) {
        const modal = document.createElement('div');
        modal.className = 'booking-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Booking Confirmation</h2>
                <p>Thank you for your booking! We've received your request.</p>
                <div class="booking-details">
                    <p><strong>Name:</strong> ${bookingData.name}</p>
                    <p><strong>Email:</strong> ${bookingData.email}</p>
                    <p><strong>Check-in:</strong> ${bookingData['check-in']}</p>
                    <p><strong>Check-out:</strong> ${bookingData['check-out']}</p>
                    <p><strong>Room Type:</strong> ${bookingData['room-type']}</p>
                    <p><strong>Package:</strong> ${bookingData['package-type']}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="btn btn-primary">Close</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Add modal styles
        const style = document.createElement('style');
        style.textContent = `
            .booking-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
            }
            .modal-content {
                background: white;
                padding: 2rem;
                border-radius: 15px;
                max-width: 500px;
                width: 90%;
                text-align: center;
            }
            .booking-details {
                text-align: left;
                margin: 1rem 0;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 10px;
            }
        `;
        document.head.appendChild(style);
    }
}

// Initialize booking form when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new BookingForm();
});

// Contact form functionality
const contactForm = document.querySelector('.contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Simulate form submission
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.textContent = 'Message Sent!';
            submitBtn.style.background = '#27ae60';
            
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.background = '';
                contactForm.reset();
            }, 2000);
        }, 1500);
    });
}

// Add hover effects for cards
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.feature-card, .room-card, .package-card, .amenity-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Add scroll progress indicator
const progressBar = document.createElement('div');
progressBar.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    z-index: 10001;
    transition: width 0.3s ease;
`;
document.body.appendChild(progressBar);

window.addEventListener('scroll', () => {
    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    progressBar.style.width = scrolled + '%';
}); 
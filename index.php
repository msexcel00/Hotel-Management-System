<?php
session_start();
// Include the database connection and fetch rooms logic here (as previously defined)
require_once 'config.php'; 

// --- 1. Fetch available rooms to populate the dropdown and preview ---
$rooms = []; // Initialize as an empty array to prevent the "Undefined variable" warning
try {
    // $pdo must be available from config.php
    $stmt = $pdo->query("SELECT room_id, room_name, price FROM rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If there is a database error, this will print it
    echo "Database Error: " . $e->getMessage(); 
    // You should also verify that the 'rooms' table exists and has data.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    </head>
<body>
<header class="navbar">
    <div class="logo">
        <a href="index.php">
            **Deluxe Hotel**
        </a>
    </div>
    
    <div class="menu-icon" onclick="toggleNav()">&#9776;</div>

    <nav id="mobileNav">
        <a href="#" class="close-btn" onclick="toggleNav()">&times;</a> 
        
        <a href="index.php" class="nav-link current">Home</a>
        <a href="rooms.php" class="nav-link">Rooms & Suites</a>
        
        <div class="dropdown">
            <button class="nav-link dropbtn">Services</button>
            <div class="dropdown-content">
                <a href="#">Concierge Services</a>
                <a href="#">Fitness Center</a>
                <a href="#">Spa</a>
                <a href="#">Breakfast Buffet</a>
                <a href="#">Laundry Services</a>
                <a href="#">Hair Salon</a>
                <a href="#">Executive Buisness Lounge</a>
                <a href="#">Meeting Facilities</a>
                </div>
        </div>
        <a href="gallery.php" class="nav-link">Gallery</a>
        <a href="#contact" class="nav-link">Contact Us</a>
    </nav>
    
    <a href="admin_login.php" class="admin-link">Admin Login</a>
</header>

<section class="hero-section">
    <div class="hero-content">
        <h1>Your Luxurious Coastal Escape Awaits.</h1>
        <p>Book direct with **Deluxe Hotel** for exclusive offers and the lowest price guarantee.</p>
    <a href="rooms.php" class="hero-button">View Rooms & Book</a>
    </div>
    
</section>

<section class="room-preview-section" id="rooms-preview">
    <div class="container">
        <h2 class="section-title">Our Luxurious Accommodation</h2>
        <p class="section-subtitle">A collection of rooms and suites designed for ultimate comfort and elegance.</p>

        <div class="room-card-grid">
            
            <?php 
            // Reuse the $rooms array fetched at the top of index.php
            // We'll limit this preview to the first 3 rooms for a clean look
            $preview_limit = 3; 
            $count = 0;
            foreach ($rooms as $room): 
                if ($count >= $preview_limit) break; 
                $count++;
            ?>
                <div class="room-card">
                    <img src="images/room-<?php echo htmlspecialchars($room['room_id']); ?>.jpg" alt="<?php echo htmlspecialchars($room['room_name']); ?>" class="room-image">
                    <div class="room-info">
                        <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                        <p class="room-price">
                            Starting from **$<?php echo number_format($room['price'], 2); ?>** / night
                        </p>
                        <p class="room-description">
                            The perfect blend of modern comfort and classic luxury, ideal for your peaceful stay.
                            </p>
                        <a href="rooms.php?room_id=<?php echo htmlspecialchars($room['room_id']); ?>" class="details-btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        
        <div class="view-all-cta">
            <a href="rooms.php" class="submit-btn">Explore All Rooms & Suites</a>
        </div>
    </div>
</section>

<section class="amenities-section">
    <div class="container">
        <h2 class="section-title">Luxury Services Included</h2>
        <p class="section-subtitle">Everything you need for a truly relaxing and productive stay at Deluxe Hotel.</p>

        <div class="services-grid">
            
            <div class="service-item">
                <span class="service-icon">⭐</span> 
                <h3>Concierge Services</h3>
                <p>Personalized assistance with reservations, local tours, and transport arrangements 24/7.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">💪</span>
                <h3>Fitness Center</h3>
                <p>State-of-the-art gym equipment available daily for your workout needs.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">💆‍♀️</span>
                <h3>Spa</h3>
                <p>Indulge in a range of massages, facials, and wellness treatments.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">🍳</span>
                <h3>Breakfast Buffet</h3>
                <p>Complimentary gourmet breakfast served daily in our main dining hall.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">👔</span>
                <h3>Laundry Services</h3>
                <p>Professional dry cleaning and wash-and-fold services available upon request.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">✂️</span>
                <h3>Hair Salon</h3>
                <p>On-site salon offering styling, cuts, and coloring for all occasions.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">💼</span>
                <h3>Executive Business Lounge</h3>
                <p>Exclusive access to a quiet workspace with high-speed internet and complimentary refreshments.</p>
            </div>
            
            <div class="service-item">
                <span class="service-icon">🗓️</span>
                <h3>Meeting Facilities</h3>
                <p>Flexible meeting rooms and event spaces for corporate functions and gatherings.</p>
            </div>

        </div>
    </div>
</section>

<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title">What Our Guests Say</h2>
        <p class="section-subtitle">Read feedback from our valued visitors.</p>

        <div class="testimonial-grid">
            
            <div class="testimonial-card">
                <p class="quote">"The Executive Suite was breathtaking! The staff attention to detail was impeccable, truly living up to the 'Deluxe' name. We will definitely be returning."</p>
                <p class="author">**— David S.**, New York</p>
            </div>
            
            <div class="testimonial-card">
                <p class="quote">"Booking was seamless thanks to the clear website availability check. The breakfast buffet was a fantastic start to every day."</p>
                <p class="author">**— Maria L.**, London</p>
            </div>
            
            <div class="testimonial-card">
                <p class="quote">"I appreciated the business lounge access and fast Wi-Fi. It allowed me to work remotely without any hassle. Highly recommended for business travel."</p>
                <p class="author">**— Kenji T.**, Tokyo</p>
            </div>
            
        </div>
    </div>
</section>

<footer class="main-footer" id="contact">
    <div class="container footer-content">
        
        <div class="footer-column contact-info">
            <h4>Deluxe Hotel</h4>
            <p>123 Luxury Avenue, Coastal City, CA 90210</p>
            <p>Phone: (555) 123-4567</p>
            <p>Email: info@deluxehotelmvp.com</p>
        </div>
        
        <div class="footer-column quick-links">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a>
            <a href="rooms.php">Rooms & Suites</a>
            <a href="gallery.php">Gallery</a>
            <a href="#amenities">Services</a>
        </div>
        
        <div class="footer-column legal-links">
            <h4>Information</h4>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
            <a href="admin_login.php">Admin Login</a>
        </div>
        
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Deluxe Hotel MVP. All rights reserved.</p>
    </div>
</footer>

<script>
    function toggleNav() {
        const nav = document.getElementById("mobileNav");
        // Toggles the 'open' class which moves the menu in/out via CSS transition
        nav.classList.toggle('open');
    }
</script>
            </body>
            </html>
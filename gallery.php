<?php
// Start session and include config (DB connection)
session_start();
require_once 'config.php';

// --- GALLERY DATA ---
$galleryImages = [
    // You will need to create dummy images in your project folder named these paths:
    ['path' => 'images/gallery-exterior.jpg', 'caption' => 'The Grand Entrance and Facade of Deluxe Hotel.'],
    ['path' => 'images/gallery-lobby.jpg', 'caption' => 'The Luxurious Lobby and Reception Area.'],
    ['path' => 'images/gallery-pool.jpg', 'caption' => 'Our Stunning Rooftop Pool with Ocean Views.'],
    ['path' => 'images/room-executive-suite.jpg', 'caption' => 'The Opulent Executive Suite Living Area.'],
    ['path' => 'images/amenity-spa.jpg', 'caption' => 'The Serene Hotel Spa and Wellness Center.'],
    ['path' => 'images/amenity-restaurant.jpg', 'caption' => 'Fine Dining at Our Signature Restaurant.'],
    ['path' => 'images/amenity-fitness.jpg', 'caption' => 'Modern, Fully Equipped Fitness Center.'],
    ['path' => 'images/dining-terrace.jpg', 'caption' => 'Al Fresco Dining on the Ocean Terrace.'],
    ['path' => 'images/room-bathroom.jpg', 'caption' => 'Marble and Gold Bathroom in the Deluxe Room.'],
    ['path' => 'images/amenity-bar.jpg', 'caption' => 'The Stylish Hotel Cocktail Bar.'],
    ['path' => 'images/meeting-facility.jpg', 'caption' => 'State-of-the-Art Meeting Facilities.'],
    ['path' => 'images/room-balcony.jpg', 'caption' => 'Private Balcony overlooking the Coastline.'],
    ['path' => 'images/guest-lounge.jpg', 'caption' => 'Relaxing Guest Lounge and Library.'],
    ['path' => 'images/night-view.jpg', 'caption' => 'Hotel Facade Lit Up at Night.'],
    ['path' => 'images/breakfast-spread.jpg', 'caption' => 'Gourmet Breakfast Buffet Spread.'],
    ['path' => 'images/kids-club.jpg', 'caption' => 'Safe and Fun Kids\' Activity Center.'],
    ['path' => 'images/hallway.jpg', 'caption' => 'Elegant and Quiet Guest Floor Hallway.'],
    ['path' => 'images/spa-treatment.jpg', 'caption' => 'Relaxing Massage Treatment Room.'],
    ['path' => 'images/pool-side.jpg', 'caption' => 'Comfortable Loungers by the Poolside.'],
    ['path' => 'images/welcome-amenities.jpg', 'caption' => 'A Special Welcome Amenity for Every Guest.'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery - Deluxe Hotel</title>
    <link rel="stylesheet" href="style.css">
    </head>
        <style>
        /* --- Gallery Page Styling --- */
.gallery-grid-section {
    padding: 40px 0;
    background-color: white;
}

/* Grid Container */
.gallery-grid {
    display: grid;
    /* Create a responsive photo grid that flows nicely */
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
    gap: 15px;
}

/* Individual Gallery Item */
.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    cursor: zoom-in;
}

.gallery-item img {
    width: 100%;
    height: 300px; /* Fixed height for consistent rows */
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.05); /* Zoom effect on hover */
}

/* Caption Overlay */
.caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 51, 102, 0.7); /* Primary Blue transparent background */
    color: white;
    padding: 10px;
    font-size: 0.9em;
    font-weight: 500;
    opacity: 0;
    transition: opacity 0.3s;
}

.gallery-item:hover .caption {
    opacity: 1;
}

/* Ensure the hero section at the top of the gallery page looks good */
.page-hero {
    background-color: #f0f4f7;
    padding: 60px 0;
    text-align: center;
}
.page-hero h1 {
    color: #003366;
    font-size: 3em;
}
    </style>
<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.php">**Deluxe Hotel**</a>
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

    <main>
        <section class="page-hero">
            <div class="container">
                <h1>Hotel Gallery</h1>
                <p>A visual tour of our facilities, rooms, and luxurious services.</p>
            </div>
        </section>

        <section class="gallery-grid-section">
            <div class="container">
                <div class="gallery-grid">
                    <?php foreach ($galleryImages as $image): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($image['path']); ?>" alt="<?php echo htmlspecialchars($image['caption']); ?>">
                            <div class="caption"><?php echo htmlspecialchars($image['caption']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer: copied from index.php -->
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
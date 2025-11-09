<?php
session_start();
require_once 'config.php'; 

// --- 1. Fetch ALL room details ---
$allRooms = [];
try {
    // Select all room details from the rooms table
    $stmt = $pdo->query("SELECT room_id, room_name, price, total_units FROM rooms ORDER BY price ASC");
    $allRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error gracefully
    $error = "Could not retrieve room data. Please try again later.";
}

// Dummy descriptions for presentation (in a real app, these would be in the database)
$descriptions = [
    'Standard Single' => 'A cozy, well-appointed room featuring one plush double bed, ideal for solo travelers or short business stays. Enjoy complimentary high-speed Wi-Fi.',
    'Double Deluxe' => 'Spacious and elegant, this room offers two queen beds or one king bed, a sitting area, and a luxurious marble bathroom. Perfect for families or couples.',
    'Executive Suite' => 'Our most luxurious offering, featuring a separate living room, dining area, a private balcony with ocean views, and exclusive access to the business lounge.'
];
?>

<?php
// ... [Existing PHP Code: session_start(), require_once 'config.php', and database fetch into $allRooms] ...

// --- DEFINITION OF ROOM FEATURES AND DESCRIPTIONS ---
// This data drives the content for all room cards
$roomDetails = [
    'Standard Single' => [
        'description' => 'A cozy, well-appointed room featuring one plush double bed, ideal for solo travelers or short business stays. Offers excellent value and comfort.',
        'included' => ['Free Wi-Fi', 'Complimentary Breakfast', 'Mini-Fridge', 'Hair Dryer'],
        'excluded' => ['Ocean View', 'Private Balcony', 'Executive Lounge Access'],
        'max_guests' => 2,
    ],
    'Double Deluxe' => [
        'description' => 'Spacious and elegant, this room offers two queen beds or one king bed, a comfortable sitting area, and a luxurious marble bathroom. Perfect for families or couples.',
        'included' => ['Free Wi-Fi', 'Complimentary Breakfast', 'Luxury Bathrobes', 'Coffee Maker', 'Sitting Area'],
        'excluded' => ['Private Balcony', 'Executive Lounge Access'],
        'max_guests' => 4,
    ],
    'Executive Suite' => [
        'description' => 'Our most luxurious offering, featuring a separate living room, dining area, a private balcony with stunning views, and exclusive access to the business lounge.',
        'included' => ['Free Wi-Fi', 'Complimentary Breakfast', 'Private Balcony', 'Separate Living Room', 'Executive Lounge Access', 'Premium Mini-Bar'],
        'excluded' => [], 
        'max_guests' => 4,
    ],
    'Family Apartment' => [
        'description' => 'Perfect for larger groups, this apartment features two bedrooms, a full kitchen, and a dining area, offering all the comforts of home.',
        'included' => ['Full Kitchen', 'Two Separate Bedrooms', 'Laundry Facilities', 'Complimentary Breakfast', 'Free Wi-Fi'],
        'excluded' => ['Private Balcony', 'Executive Lounge Access'],
        'max_guests' => 6,
    ],
    'Penthouse Suite' => [
        'description' => 'The height of luxury. This suite occupies the top floor with panoramic ocean views, a private jacuzzi, and dedicated butler service.',
        'included' => ['Private Balcony', 'Butler Service', 'Jacuzzi', 'Separate Living Room', 'All Amenities'],
        'excluded' => [], 
        'max_guests' => 2,
    ],
    'Accessible Room' => [
        'description' => 'Designed for comfort and safety, this room offers wider doorways, roll-in showers, and easy access to all hotel facilities.',
        'included' => ['Roll-in Shower', 'Grab Bars', 'Complimentary Breakfast', 'Free Wi-Fi', 'Easy Access'],
        'excluded' => ['Ocean View', 'Private Balcony'],
        'max_guests' => 2,
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rooms & Suites - Deluxe Hotel</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    /* --- Rooms Page Specific Styles --- */

.page-hero {
    background-color: #f0f4f7;
    padding: 60px 0;
    text-align: center;
}
.page-hero h1 {
    color: #003366;
    font-size: 3em;
}
.page-hero p {
    color: #666;
    margin-top: 10px;
    font-size: 1.1em;
}

.room-listing-section {
    padding: 40px 0;
}
/* --- Rooms Page Specific Styles for Denser Cards --- */

/* Full Room Card Layout */
.room-full-card {
    display: flex;
    flex-wrap: wrap; 
    margin-bottom: 30px; /* Reduced margin */
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Lighter shadow */
    background-color: white;
}

.room-image-area {
    flex: 1 1 30%; /* Image takes 30% for a denser look (was 40%) */
    min-width: 250px;
    max-height: 320px; /* Fixed max height for all cards */
    overflow: hidden;
}
.room-image-area img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.room-details-area {
    flex: 1 1 65%; /* Details take the majority of the space */
    padding: 25px 30px; /* Tighter padding */
    display: flex;
    flex-direction: column;
}

.room-details-area h2 {
    color: #003366;
    font-size: 1.8em;
    margin-bottom: 10px;
    border-bottom: 2px solid #C8A252;
    padding-bottom: 5px;
}

.room-description-full {
    color: #444;
    line-height: 1.5;
    margin-bottom: 20px;
}

/* Feature Lists Styling */
.feature-list-group {
    display: flex;
    gap: 50px;
    margin-bottom: 15px;
    flex-grow: 1; /* Pushes price/button to the bottom */
}

.features-included, .features-excluded {
    flex: 1;
}

.features-included h4, .features-excluded h4 {
    font-size: 1em;
    color: #003366;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.room-features {
    list-style: none;
    padding-left: 0;
    font-size: 0.9em; /* Smaller text for density */
}
.room-features li {
    padding: 3px 0;
    color: #333;
}
.room-features span {
    margin-right: 5px;
    font-weight: bold;
}
.room-features .excluded span {
    color: #DC3545 !important; 
}


/* Booking Info */
.room-booking-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px dashed #eee;
}

.price-tag {
    font-size: 2.2em; /* Slightly smaller price tag */
}

/* --- New: Booking Modal Styles (Full Screen Overlay) --- */
.modal-overlay {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 200; /* High z-index to be on top of everything */
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%;
    overflow: auto; 
    background-color: rgba(0, 0, 0, 0.7); /* Black w/ opacity */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
}

.modal-content {
    background-color: white;
    margin: auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 450px; /* Keep the booking form compact */
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    animation-name: animatetop;
    animation-duration: 0.4s;
}

/* Close Button (X) */
.close-btn {
    color: #003366;
    float: right;
    font-size: 36px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
    z-index: 201;
}
.close-btn:hover,
.close-btn:focus {
    color: #C8A252; /* Gold accent */
    text-decoration: none;
    cursor: pointer;
}

/* Re-use booking-widget styles for the form itself inside the modal */
.modal-content .booking-widget {
    background-color: transparent; /* Widget background is the modal content background */
    box-shadow: none; 
    width: 100%; 
    padding: 35px;
    padding-top: 60px; /* Space for the close button */
}

/* Modal form controls styling */
.modal-content .booking-widget label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #003366;
    font-size: 0.95em;
}
.modal-content .booking-widget input[type="text"],
.modal-content .booking-widget input[type="email"],
.modal-content .booking-widget input[type="date"],
.modal-content .booking-widget select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
    box-sizing: border-box;
    margin-bottom: 14px;
    font-size: 1em;
}
.modal-content .booking-widget input:focus,
.modal-content .booking-widget select:focus {
    outline: none;
    border-color: #C8A252;
    box-shadow: 0 0 0 3px rgba(200,162,82,0.12);
}
.modal-content .booking-widget .error-message {
    margin-bottom: 10px;
    color: #DC3545;
    font-weight: 600;
}

/* Ensure modal CTA uses primary color and spans full width */
.modal-content .booking-widget .submit-btn {
    display: block;
    width: 100%;
    padding: 12px 16px;
    background-color: #C8A252; /* Primary gold */
    color: #fff;
    border-radius: 8px;
    font-weight: 700;
    text-align: center;
    border: none;
}
.modal-content .booking-widget .submit-btn:hover {
    background-color: #A88542;
    transform: none;
}

/* Modal Animation */
@keyframes animatetop {
    from {top: -300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* Media Query for responsiveness */
@media (max-width: 768px) {
    .room-full-card {
        flex-direction: column; /* Stack image and details vertically */
    }
    .room-image-area, .room-details-area {
        flex: 1 1 100%;
    }
}
</style>
<body>
<!-- Alerts removed: validation/messages handled in-modal via JS -->
    <main>

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
                <a href="#concierge">Concierge Services</a>
                <a href="#fitness">Fitness Center</a>
                <a href="#spa">Spa</a>
                <a href="#breakfast">Breakfast Buffet</a>
                <a href="#laundry">Laundry Services</a>
                <a href="#salon">Hair Salon</a>
                <a href="#lounge">Executive Business Lounge</a>
                <a href="#meetings">Meeting Facilities</a>
            </div>
        </div>

        <a href="gallery.php" class="nav-link">Gallery</a>
        <a href="#contact" class="nav-link">Contact Us</a>
    </nav>
    <a href="admin_login.php" class="admin-link">Admin Login</a>
</header>

        <section class="page-hero">
            <div class="container">
                <h1>Our Accommodations</h1>
                <p>Find the perfect space for your stay at Deluxe Hotel. All rooms include premium amenities.</p>
            </div>
        </section>
     
        <section class="room-listing-section">
    <div class="container">
        <div class="room-listings-content"> 

            <?php if (empty($allRooms)): ?>
                <p class="error-message">No room listings are currently available.</p>
            <?php else: ?>

                <?php foreach ($allRooms as $room): 
                    $details = $roomDetails[$room['room_name']] ?? []; // Get detailed feature set
                ?>
                    <div class="room-full-card">
                        
                        <div class="room-image-area">
                            <img src="/deluxe_hotel/images/room-<?php echo htmlspecialchars($room['room_id']); ?>.jpg" ... >
                        </div>
                        
                        <div class="room-details-area">
                            
                            <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
                            
                            <p class="room-description-full">
                                <?php echo htmlspecialchars($details['description'] ?? "No description available."); ?>
                            </p>

                            <div class="feature-list-group">
                                
                                <div class="features-included">
                                    <h4>Included Features:</h4>
                                    <ul class="room-features">
                                        <li><span>👨‍👩‍👧‍👦</span> Max Guests: **<?php echo $details['max_guests']; ?>**</li>
                                        <?php foreach ($details['included'] as $feature): ?>
                                            <li><span>✅</span> <?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <?php if (!empty($details['excluded'])): ?>
                                <div class="features-excluded">
                                    <h4>Not Included:</h4>
                                    <ul class="room-features excluded">
                                        <?php foreach ($details['excluded'] as $feature): ?>
                                            <li><span>❌</span> <?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="room-booking-info">
                                <span class="price-tag">
                                    $<?php echo number_format($room['price'], 2); ?> 
                                    <span>/ night</span>
                                </span>
                                <a href="#" onclick="openBookingModal(<?php echo htmlspecialchars($room['room_id']); ?>)" class="details-btn book-now-btn">Book Now</a>
                            </div>
                        </div> <!-- .room-details-area -->
                    </div> <!-- .room-full-card -->
                <?php endforeach; ?>
            
            <?php endif; ?>
        </div>
        
        </div>
   </section>
  </main>

     <div id="bookingModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        
        <div class="booking-widget">
            <h2>Check Availability & Book</h2>
            
            <form id="modalBookingForm" action="process_booking.php" method="POST">
                 
                 <p class="error-message" id="modal-error-display" style="color:#DC3545;"></p>

                 <label for="modal_room_id">Select Room Type:</label>
                <select name="room_id" id="modal_room_id" required>
                   <option value="">-- Choose a Room --</option>
                      <?php foreach ($allRooms as $room): ?>
                     <option value="<?php echo htmlspecialchars($room['room_id']); ?>"
                           data-price="<?php echo htmlspecialchars($room['price']); ?>"> <?php echo htmlspecialchars($room['room_name']) . " ($" . htmlspecialchars($room['price']) . " / night)"; ?>
                      </option>
                    <?php endforeach; ?>
               </select>

             <div id="priceDisplay" style="margin-top: 15px; font-size: 1.3em; color: #003366; font-weight: bold;">
             Total Cost: $0.00
                </div>
                 
                 <label for="modal_check_in_date">Check-in Date:</label>
                 <input type="date" name="check_in_date" id="modal_check_in_date" required>
                 
                 <label for="modal_check_out_date">Check-out Date:</label>
                 <input type="date" name="check_out_date" id="modal_check_out_date" required>
     
                 <label for="modal_guest_name">Your Name:</label>
                 <input type="text" name="guest_name" id="modal_guest_name" required>
                 
                 <label for="modal_guest_email">Your Email:</label>
                 <input type="email" name="guest_email" id="modal_guest_email" required>
                 
                 <button type="submit" class="submit-btn">Check Availability & Book</button>
            </form>
        </div>
    </div>
</div>

      <script>
    // --- 1. Modal Control Functions ---
    const modal = document.getElementById('bookingModal');
    const closeModalBtn = document.querySelector('.close-btn');
    const roomSelect = document.getElementById('modal_room_id');

    // Function to OPEN the modal and pre-select a room
    function openBookingModal(roomId = null) {
        modal.style.display = 'flex';
        // Pre-select the room if an ID was passed
        if (roomId) {
            roomSelect.value = roomId;
        }
    }

    // Function to CLOSE the modal
    function closeBookingModal() {
        modal.style.display = 'none';
        // Optional: Clear form errors on close
        document.getElementById('modal-error-display').textContent = '';
    }

    // Close when the 'x' is clicked
    closeModalBtn.onclick = closeBookingModal;

    // Close when clicking outside the modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            closeBookingModal();
        }
    }
    
    // --- 2. Implement Basic Form Validation (From Step 7) ---
    // You would integrate the full form validation logic here as planned for Step 7.
    // For now, ensure your inputs have the required IDs (check 2A).
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Form Element Variables ---
        const form = document.getElementById('modalBookingForm');
        const checkInDateInput = document.getElementById('modal_check_in_date');
        const checkOutDateInput = document.getElementById('modal_check_out_date');
        const roomSelect = document.getElementById('modal_room_id');
    const errorDisplay = document.getElementById('modal-error-display'); 
    const priceDisplay = document.getElementById('priceDisplay');
        const guestNameInput = document.getElementById('modal_guest_name');
        const guestEmailInput = document.getElementById('modal_guest_email');
    /* Use the existing modal error display area for AJAX feedback (ajaxFeedback element was not present) */
    const ajaxFeedback = errorDisplay; 
        const submitButton = form.querySelector('.submit-btn'); // Assuming this element exists

        const today = new Date();
        today.setHours(0, 0, 0, 0); 
        const todayISO = today.toISOString().split('T')[0];
        
        // Set the minimum check-in date allowed (today's date)
        checkInDateInput.setAttribute('min', todayISO);

// --- NEW AJAX SUBMISSION LOGIC (with Validation) ---
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // ALWAYS prevent default submission first
        
        // --- START: Client-side validation ---
        let isValid = true;
        let errorMessage = '';
        errorDisplay.textContent = ''; // Clear old errors

        const checkIn = new Date(checkInDateInput.value);
        const checkOut = new Date(checkOutDateInput.value);

        if (roomSelect.value === '' || checkInDateInput.value === '' || checkOutDateInput.value === '') {
            errorMessage = 'Please select a room and enter check-in/out dates.';
            isValid = false;
        } else if (checkIn < today) {
            errorMessage = 'Check-in date cannot be in the past.';
            isValid = false;
        } else if (checkOut <= checkIn) {
            errorMessage = 'Check-out date must be AFTER the check-in date.';
            isValid = false;
        }
        
        if (!isValid) { 
            // Display validation error
            errorDisplay.textContent = 'Booking Error: ' + errorMessage;
            errorDisplay.style.padding = '10px';
            errorDisplay.style.border = '1px solid #DC3545';
            errorDisplay.style.backgroundColor = '#ffeeee';
            return; // Stop processing
        }
        // --- END: Client-side validation ---

        
        // --- Start AJAX Call (Only if validation passed) ---
        const formData = new FormData(form);

        ajaxFeedback.innerHTML = '<span style="color: #003366;">Checking availability...</span>';
        submitButton.disabled = true;

        fetch('check_availability_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            
            if (data.available) {
                ajaxFeedback.innerHTML = `<span style="color: green; font-weight: bold;">${data.message}</span>`;
                // Manually submit the form to process_booking.php
                setTimeout(() => { form.submit(); }, 500); 
            } else {
                ajaxFeedback.innerHTML = `<span style="color: red; font-weight: bold;">${data.message}</span>`;
            }
        })
        .catch(error => {
            ajaxFeedback.innerHTML = `<span style="color: red;">Error: Could not check server.</span>`;
            submitButton.disabled = false;
        });
    });
        
        // --- 3. Modal Control Functions (Ensure these are here too!) ---
        const modal = document.getElementById('bookingModal');
        const closeModalBtn = document.querySelector('.close-btn');

        // Function to OPEN the modal (triggered by 'Book Now' button)
        window.openBookingModal = function(roomId = null) {
            modal.style.display = 'flex';
            if (roomId) {
                roomSelect.value = roomId;
            }
        }

        // Function to CLOSE the modal
        function closeBookingModal() {
            modal.style.display = 'none';
            errorDisplay.textContent = ''; // Clear errors on close
        }

        closeModalBtn.onclick = closeBookingModal;
        window.onclick = function(event) {
            if (event.target == modal) {
                closeBookingModal();
            }
        }

        /* Price calculation helper and listeners must run inside DOMContentLoaded so
           they can access check-in/out inputs defined above in this scope. */
        function calculatePrice() {
            // 1. Get room price
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const price = selectedOption ? selectedOption.getAttribute('data-price') : null;

            // 2. Get dates
            const checkIn = checkInDateInput.value;
            const checkOut = checkOutDateInput.value;

            if (!price || !checkIn || !checkOut) {
                priceDisplay.textContent = 'Total Cost: $0.00';
                return;
            }

            // 3. Calculate number of nights
            const date1 = new Date(checkIn);
            const date2 = new Date(checkOut);

            // Ensure check-out is after check-in
            if (date2 <= date1) {
                priceDisplay.textContent = 'Total Cost: Invalid Dates';
                return;
            }

            const timeDiff = Math.abs(date2.getTime() - date1.getTime());
            const diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days

            // 4. Calculate total cost
            const totalCost = (diffDays * parseFloat(price)).toFixed(2);

            priceDisplay.textContent = `Total Cost: $${totalCost} (${diffDays} nights)`;
        }

        // Add event listeners to trigger the calculation on change
        roomSelect.addEventListener('change', calculatePrice);
        checkInDateInput.addEventListener('change', calculatePrice);
        checkOutDateInput.addEventListener('change', calculatePrice);

        // Run calculation initially in case a room was preselected via URL
        calculatePrice();
    });
</script>
</body>
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
</html>

<script>
    function toggleNav() {
        const nav = document.getElementById("mobileNav");
        // Toggles the 'open' class which moves the menu in/out via CSS transition
        nav.classList.toggle('open');
    }
</script>
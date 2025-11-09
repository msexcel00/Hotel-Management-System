<?php
// 1. Include the database connection and start the session
require_once 'config.php'; 
session_start();

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: rooms.php"); // Redirect to rooms page
    exit;
}

// 2. Collect and Sanitize Input
$roomId      = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
$checkIn     = filter_input(INPUT_POST, 'check_in_date', FILTER_SANITIZE_SPECIAL_CHARS);
$checkOut    = filter_input(INPUT_POST, 'check_out_date', FILTER_SANITIZE_SPECIAL_CHARS);
$guestName   = filter_input(INPUT_POST, 'guest_name', FILTER_SANITIZE_SPECIAL_CHARS);
$guestEmail  = filter_input(INPUT_POST, 'guest_email', FILTER_VALIDATE_EMAIL);

// 3. Basic Server-Side Validation
if (!$roomId || !$checkIn || !$checkOut || !$guestName || !$guestEmail) {
    $_SESSION['error'] = "Please fill out all required fields correctly.";
    header("location: rooms.php"); // Send error back to rooms page
    exit;
}

// Ensure check-out is after check-in and both are not in the past
if ($checkOut <= $checkIn || $checkIn < date('Y-m-d')) {
    $_SESSION['error'] = "Invalid dates selected. Check-out must be after check-in, and dates cannot be in the past.";
    header("location: rooms.php"); // Send error back to rooms page
    exit;
}

// 4. Start the Booking Logic (TRY block)
try {
    // 4a. Get the total available units for this room type
    $stmt = $pdo->prepare("SELECT total_units, price FROM rooms WHERE room_id = ?");
    $stmt->execute([$roomId]);
    $roomData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$roomData) {
        $_SESSION['error'] = "Selected room type is invalid.";
        header("location: rooms.php");
        exit;
    }

    $totalUnits = $roomData['total_units'];
    $roomPrice  = $roomData['price'];

    // 4b. Count overlapping bookings
    $stmt = $pdo->prepare("
        SELECT COUNT(booking_id) AS booked_count
        FROM bookings
        WHERE room_id = :room_id
        AND check_out_date > :check_in
        AND check_in_date < :check_out
    ");

    $stmt->execute([
        ':room_id'  => $roomId,
        ':check_in' => $checkIn,
        ':check_out' => $checkOut
    ]);

    $bookedCount = $stmt->fetchColumn();

    // 4c. Final Check
    if ($bookedCount >= $totalUnits) {
        $_SESSION['error'] = "Sorry, that room type is fully booked for your selected dates.";
        header("location: rooms.php");
        exit;
    }

    // --- 5. CALCULATE COST AND INSERT DATA ---

    // Calculate total cost
    $checkInTime = new DateTime($checkIn);
    $checkOutTime = new DateTime($checkOut);
    $interval = $checkInTime->diff($checkOutTime);
    $nights = $interval->days;

    if ($nights < 1) {
         $_SESSION['error'] = "Booking must be for at least one night.";
         header("location: rooms.php");
         exit;
    }

    $totalCost = $nights * $roomPrice;

    // Generate the key
    $cancellation_key = md5(uniqid(rand(), true));

    // Securely insert the new booking record
    $sql = "INSERT INTO bookings (room_id, guest_name, guest_email, check_in_date, check_out_date, total_cost, status, cancellation_key) 
            VALUES (:rid, :gname, :gemail, :cin, :cout, :cost, 'Confirmed', :cancellation_key)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':rid'    => $roomId,
        ':gname'  => $guestName,
        ':gemail' => $guestEmail,
        ':cin'    => $checkIn,
        ':cout'   => $checkOut,
        ':cost'   => $totalCost,
        ':cancellation_key' => $cancellation_key
    ]);

    // Get the last inserted ID
    $lastId = $pdo->lastInsertId();

    // Success! Redirect to a confirmation page
    header("location: confirmation.php?booking_id=" . $lastId . "&key=" . $cancellation_key);
    exit;

} catch (PDOException $e) {
    // 5. Catch any database error
    $_SESSION['error'] = "A system error occurred during booking. Please try again. Error: " . $e->getMessage();
    header("location: rooms.php");
    exit;
}
?>
<?php
session_start();
require_once 'config.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

// 1. Get the booking ID from the URL
$bookingId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($bookingId) {
    try {
        // 2. Execute DELETE command using a prepared statement for security
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?");
        $stmt->execute([$bookingId]);

        // Success message for the user (optional, can use session messages)
        $_SESSION['message'] = "Booking #{$bookingId} successfully deleted.";

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting booking: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid booking ID provided.";
}

// 3. Redirect back to the dashboard
header("location: admin_dashboard.php");
exit;
?>
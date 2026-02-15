<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}


$bookingId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($bookingId) {
    try {
        
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?");
        $stmt->execute([$bookingId]);

        
        $_SESSION['message'] = "Booking #{$bookingId} successfully deleted.";

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting booking: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid booking ID provided.";
}


header("location: admin_dashboard.php");
exit;
?>
<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}


$bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
$newStatus = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);

$allowedStatuses = ['Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled'];

if ($bookingId && in_array($newStatus, $allowedStatuses)) {
    try {
        
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
        $stmt->execute([$newStatus, $bookingId]);

        $_SESSION['message'] = "Booking #{$bookingId} status updated to '{$newStatus}'.";

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating status: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request or status.";
}


header("location: admin_dashboard.php");
exit;
?>
<?php
require_once 'config.php'; 
header('Content-Type: application/json');

$response = ['available' => false, 'message' => 'Invalid request.'];

// 1. Get Input
$roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
$checkIn = filter_input(INPUT_POST, 'check_in_date', FILTER_SANITIZE_SPECIAL_CHARS);
$checkOut = filter_input(INPUT_POST, 'check_out_date', FILTER_SANITIZE_SPECIAL_CHARS);

if ($roomId && $checkIn && $checkOut) {
    try {
        // 2. Get Total Units
        $stmt = $pdo->prepare("SELECT total_units FROM rooms WHERE room_id = ?");
        $stmt->execute([$roomId]);
        $totalUnits = $stmt->fetchColumn();

        // 3. Count Overlapping Bookings (Same logic as process_booking.php)
        $stmt = $pdo->prepare("
            SELECT COUNT(booking_id) AS booked_count
            FROM bookings
            WHERE room_id = :room_id
            AND check_out_date > :check_in
            AND check_in_date < :check_out
        ");

        $stmt->execute([':room_id' => $roomId, ':check_in' => $checkIn, ':check_out' => $checkOut]);
        $bookedCount = $stmt->fetchColumn();
        
        $availableCount = $totalUnits - $bookedCount;

        // 4. Determine Availability
        if ($availableCount > 0) {
            $response['available'] = true;
            $response['message'] = "Great! We have {$availableCount} rooms of this type available.";
        } else {
            $response['available'] = false;
            $response['message'] = "Sorry, this room type is fully booked for these dates.";
        }

    } catch (PDOException $e) {
        $response['message'] = 'System Error during check.';
    }
}

echo json_encode($response);
?>
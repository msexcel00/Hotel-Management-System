<?php
// 1. Include the database connection
require_once 'config.php'; 

// 2. Get the booking ID from the URL
$bookingId = filter_input(INPUT_GET, 'booking_id', FILTER_VALIDATE_INT);

// Basic validation check
if (!$bookingId) {
    die("Invalid booking ID provided.");
}

$bookingDetails = null;

try {
    // 3. Prepare and Execute Query to retrieve booking and room details
    // We join the 'bookings' and 'rooms' tables to get the room name and price.
    $stmt = $pdo->prepare("
        SELECT 
            b.*, r.room_name, r.price 
        FROM 
            bookings b
        JOIN 
            rooms r ON b.room_id = r.room_id
        WHERE 
            b.booking_id = :id
    ");

    $stmt->execute([':id' => $bookingId]);
    $bookingDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bookingDetails) {
        die("Booking not found.");
    }

} catch (PDOException $e) {
    die("Database error: Could not retrieve booking details. " . $e->getMessage());
}

// Helper function to format dates
$formatDate = function($date) {
    return date("F j, Y", strtotime($date));
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed!</title>
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    padding: 20px;
}
.container {
    max-width: 600px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
h1 {
    color: #CBA252;
    text-align: center;
}
.summary-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
.summary-table th, .summary-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.summary-table th {
    background-color: #f8f8f8;
    width: 40%;
}
.status-confirmed {
    color: green;
    font-weight: bold;
}
.cta-link {
    text-align: center;
    margin-top: 30px;
}
.cta-link{
    background: #C8A252;
    padding: 12px 16px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
}
.cta-link:hover{
    background: #A88542;
}
.cta-link a{
    color: #fff;
    text-decoration: none;
}
</style>
<body>
    <div class="container">
        <h1>🎉 Reservation Confirmed!</h1>
        <p>Thank you, **<?php echo htmlspecialchars($bookingDetails['guest_name']); ?>**, your reservation is complete.</p>
        
        <h2>Booking Summary</h2>
        <table class="summary-table">
            <tr>
                <th>Confirmation ID:</th>
                <td>**#<?php echo htmlspecialchars($bookingDetails['booking_id']); ?>**</td>
            </tr>
            <tr>
                <th>Room Type:</th>
                <td><?php echo htmlspecialchars($bookingDetails['room_name']); ?></td>
            </tr>
            <tr>
                <th>Check-in Date:</th>
                <td><?php echo $formatDate($bookingDetails['check_in_date']); ?></td>
            </tr>
            <tr>
                <th>Check-out Date:</th>
                <td><?php echo $formatDate($bookingDetails['check_out_date']); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo htmlspecialchars($bookingDetails['guest_email']); ?></td>
            </tr>
            <tr>
                <th>Total Cost:</th>
                <td>**$<?php echo number_format($bookingDetails['total_cost'], 2); ?>**</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td class="status-confirmed"><?php echo htmlspecialchars($bookingDetails['status']); ?></td>
            </tr>
        </table>

        <p class="cta-link"><a href="index.php">Book Another Stay</a></p>
        <a href="index.php" class="details-btn" style="margin-right: 15px;">Return to Homepage</a>
    </div>
</body>
</html>
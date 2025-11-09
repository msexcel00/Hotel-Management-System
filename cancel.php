<?php
require_once 'config.php'; 

$message = "Invalid or expired cancellation link.";
$success = false;

// 1. Get the key from the URL
$raw_key = $_GET['key'] ?? '';

// 2. CRITICAL FIX: Use a regular expression to extract ONLY the 32-character key
preg_match('/[a-f0-9]{32}/', $raw_key, $matches);
$cancellation_key = $matches[0] ?? ''; // This will be the pure 32-character key, or empty

// 3. Check if the key is valid
if (!empty($cancellation_key) && strlen($cancellation_key) == 32) {
    
    try {
        // 4. Look for a booking with this key
        $stmt = $pdo->prepare("SELECT status FROM bookings WHERE cancellation_key = :key");
        $stmt->bindParam(':key', $cancellation_key, PDO::PARAM_STR);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // 5. Check what we found
        if ($booking) {
            // A booking was found!
            if ($booking['status'] !== 'Cancelled') {
                // Cancel it
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'Cancelled' WHERE cancellation_key = :key");
                $stmt->bindParam(':key', $cancellation_key, PDO::PARAM_STR);
                $stmt->execute();
                
                $message = "Your reservation has been successfully CANCELLED.";
                $success = true;
            } else {
                $message = "This reservation was already cancelled.";
                $success = true;
            }
        } else {
            $message = "Cancellation failed: No booking was found with this key.";
            $success = false;
        }
        
    } catch (PDOException $e) {
        $message = "System error during cancellation.";
        $success = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cancellation Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 50px auto; text-align: center;">
        <h1 style="color: <?php echo $success ? '#28a745' : '#DC3545'; ?>;">
            <?php echo $success ? 'Cancellation Status' : 'Cancellation Failed'; ?>
        </h1>
        <p style="font-size: 1.1em; margin-bottom: 30px;"><?php echo $message; ?></p>
        <p><a href="index.php" class="details-btn">Return to Deluxe Hotel Home</a></p>
    </div>
</body>
</html>
<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // XAMPP default is 'root'
define('DB_PASSWORD', '');     // XAMPP default is an empty password
define('DB_NAME', 'hotel_mvp'); // This MUST match the name you created

// ... PDO connection code ...
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Uncomment to test connection
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
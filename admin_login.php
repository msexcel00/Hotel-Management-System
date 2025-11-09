<?php
session_start();
// Include the database connection (optional here, but good practice if checking credentials against a table)
require_once 'config.php'; 

$error = '';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    // Hardcoded credentials for MVP (Replace with database check later)
    $valid_username = 'admin';
    // NOTE: In a real app, ALWAYS hash the password and check against the hash from the database.
    $valid_password = 'password123'; 

    if ($username === $valid_username && $password === $valid_password) {
        // Login successful
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
      <link rel="stylesheet" href="style.css">
</head>
<style>
body.admin-page{
    /* Establish a stacking context for the blurred background pseudo-element */
    position: relative;
    min-height: 100vh;
    overflow: hidden;
}
/* Blurred fixed background behind the page content. Uses the project's image file name.
   Using a pseudo-element allows the form content to remain sharp while the background is blurred. */
body.admin-page::before{
    content: '';
    position: fixed;
    inset: 0; /* top:0; right:0; bottom:0; left:0; */
    background: url('deluxehotel.jpeg') center/cover no-repeat;
    background-position: center;
    background-size: cover;
    filter: blur(6px) brightness(0.55);
    z-index: -1; /* place behind content */
}
    /* --- Basic form styling for admin_login.php --- */
.container {
    max-width: 400px; /* Limit width */
    margin: 50px auto;
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.container h1 {
    text-align: center;
    color: #003366;
    margin-bottom: 25px;
}

.container label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}

.container input[type="text"],
.container input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.container button[type="submit"] {
    margin-top: 25px;
    width: 100%;
    padding: 12px;
    background-color: #C8A252; /* Gold/Accent */
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1.1em;
    cursor: pointer;
}
.container button[type="submit"]:hover {
    background-color: #A88542;
}
</style>
<body class="admin-page">
    <div class="container">
        <h1>Admin Login</h1>
        
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="admin_login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            
            <button type="submit">Log In</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="color: #003366; text-decoration: none;">&larr; Back to Deluxe Hotel Home</a>
    </p>
    </div>
</body>
</html>
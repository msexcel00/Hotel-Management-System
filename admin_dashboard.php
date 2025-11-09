<?php
session_start();
require_once 'config.php'; 

// Security check (keep this at the top)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

// --- 1. Collect Filter/Search Parameters ---
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

$whereClauses = [];
$params = [];

// --- 2. Build Dynamic WHERE Clauses ---

// A. Search by Name or Email
if (!empty($search)) {
    $whereClauses[] = "(b.guest_name LIKE :search OR b.guest_email LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// B. Filter by Status
if (!empty($status)) {
    $whereClauses[] = "b.status = :status";
    $params[':status'] = $status;
}

// --- 3. Construct the Final SQL Query ---
$sql = "SELECT b.*, r.room_name 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.room_id";

// Apply WHERE clause if any filters are active
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}

$sql .= " ORDER BY b.check_in_date DESC";

$bookings = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // In a real app, you'd log this, but for testing:
    die("Database ERROR fetching bookings: " . $e->getMessage()); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<style>   
    
    /* --- Admin Dashboard Layout --- */
.admin-container {
    max-width: 95%; /* Use a wide container for data tables */
    margin: 40px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.admin-container h1 {
    color: #003366; /* Primary Blue */
    margin-bottom: 25px;
    font-size: 2em;
}

/* Styling for the Welcome/Logout line */
.admin-container > p {
    margin-bottom: 20px;
    font-size: 1.1em;
}
.admin-container > p a {
    color: #C8A252; /* Gold Accent */
    text-decoration: none;
    font-weight: bold;
}
/* --- Admin Filter Section Styling --- */
.filter-section {
    padding: 15px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap; /* Allows stacking on smaller admin screens */
}

.filter-input, .filter-select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1em;
    min-width: 150px;
}

.filter-btn {
    padding: 8px 15px;
    background-color: #003366;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none; /* For the Reset link */
    font-weight: bold;
}

.filter-btn:hover {
    background-color: #002244;
}

.reset-btn {
    background-color: #6c757d; /* Gray color for reset button */
}
.reset-btn:hover {
    background-color: #5a6268;
}
/* --- Table Styling --- */
.dashboard-table {
    width: 100%;
    border-collapse: collapse; /* Remove double lines */
    margin-top: 20px;
    font-size: 0.95em;
}

.dashboard-table th {
    background-color: #003366; /* Primary Blue Header */
    color: white;
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #002244;
}

.dashboard-table td {
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    vertical-align: middle;
}

.dashboard-table tr:nth-child(even) {
    background-color: #f9f9f9; /* Zebra striping for readability */
}

/* Ensure the status dropdown is styled */
.dashboard-table select {
    padding: 5px 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
}
/* --- Action Links/Buttons --- */
.action-link {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.2s;
    font-size: 0.9em;
}

/* Delete/Cancel Button Styling */
.action-link.delete {
    background-color: #DC3545; /* Error Red */
    color: white;
    border: 1px solid #DC3545;
}
.action-link.delete:hover {
    background-color: #c82333; /* Darker Red on hover */
    border-color: #c82333;
}

/* Optional: View/Edit button (if added later) */
.action-link.edit {
    background-color: #C8A252; /* Gold Accent */
    color: white;
    margin-right: 5px;
}
.action-link.edit:hover {
    background-color: #A88542;
}
</style>
<body>
    <div class="admin-container" style="max-width: 90%;">
        <h1>Hotel Reservation Dashboard</h1>
        <?php 
          if (isset($_SESSION['message'])): ?>
    <div style="padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 20px;">
        <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
    </div>
<?php endif; 

        if (isset($_SESSION['error'])): ?>
    <div style="padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 20px;">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
        <p>Welcome, **<?php echo htmlspecialchars($_SESSION['username']); ?>**! | <a href="admin_logout.php">Logout</a></p>

        <div class="filter-section">
    <form action="admin_dashboard.php" method="GET" class="filter-form">
        
        <input type="text" name="search" placeholder="Search by Guest Name or Email" 
               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="filter-input">
        
        <select name="status" class="filter-select">
            <option value="">— Filter by Status —</option>
            <?php 
            $statuses = ['Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled'];
            $currentStatus = $_GET['status'] ?? '';
            foreach ($statuses as $status): ?>
                <option value="<?php echo $status; ?>" 
                        <?php if ($currentStatus == $status) echo 'selected'; ?>>
                    <?php echo $status; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="filter-btn">Apply Filters</button>
        <a href="admin_dashboard.php" class="filter-btn reset-btn">Reset</a>
    </form>
</div>

        <h2>Current Bookings (<?php echo count($bookings); ?> total)</h2>

        <?php if (empty($bookings)): ?>
            <p>No reservations found in the system.</p>
        <?php else: ?>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Email</th>
                        <th>Room Type</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Cost</th>
                        <th>Status</th>
                       <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['guest_email']); ?></td>
                            <td>**<?php echo htmlspecialchars($booking['room_name']); ?>**</td>
                            <td><?php echo date("M d, Y", strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo date("M d, Y", strtotime($booking['check_out_date'])); ?></td>
                            <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                            <td>
                    <form action="update_booking.php" method="POST" onchange="this.submit()">
                  <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                   <select name="new_status">
                   <?php 
                     $statuses = ['Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled'];
                  foreach ($statuses as $status): ?>
                   <option value="<?php echo $status; ?>" 
                     <?php if ($booking['status'] == $status) echo 'selected'; ?>>
                    <?php echo $status; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</td>
                            <td>
                                <a href="delete_booking.php?id=<?php echo htmlspecialchars($booking['booking_id']); ?>" 
                                   onclick="return confirm('Are you sure you want to delete booking #<?php echo htmlspecialchars($booking['booking_id']); ?>?');"
                                   class="action-link delete">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
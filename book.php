<?php
session_start();
require_once 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate showtime_id from URL
if (!isset($_GET['showtime_id']) || !is_numeric($_GET['showtime_id'])) {
    die("Invalid showtime.");
}

$showtime_id = (int)$_GET['showtime_id'];

// Fetch showtime details with movie info
$stmt = $pdo->prepare("
    SELECT st.*, m.title, m.poster, m.duration, m.rating 
    FROM showtimes st
    JOIN movies m ON st.movie_id = m.id
    WHERE st.id = ?
");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

if (!$showtime) {
    die("Showtime not found.");
}

$ticket_price = 10; // Assume fixed price per ticket

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tickets = (int)($_POST['tickets'] ?? 0);

    if ($tickets <= 0) {
        $errors[] = "Please select at least one ticket.";
    } elseif ($tickets > $showtime['available_seats']) {
        $errors[] = "Only {$showtime['available_seats']} seats are available.";
    }

    if (empty($errors)) {
        // Save booking
        $total_price = $tickets * $ticket_price;

        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, showtime_id, tickets, total_price, booking_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $showtime_id, $tickets, $total_price]);

        // Update available seats
        $stmt = $pdo->prepare("UPDATE showtimes SET available_seats = available_seats - ? WHERE id = ?");
        $stmt->execute([$tickets, $showtime_id]);

        $success = "Booking confirmed! You booked $tickets ticket(s) for '{$showtime['title']}' on {$showtime['show_date']} at {$showtime['show_time']}.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Tickets - <?php echo htmlspecialchars($showtime['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h2>Book Tickets for "<?php echo htmlspecialchars($showtime['title']); ?>"</h2>
    <img src="../assets/images/<?php echo htmlspecialchars($showtime['poster']); ?>" width="200" style="float:left; margin-right: 20px;">
    <p><strong>Date:</strong> <?php echo htmlspecialchars($showtime['show_date']); ?></p>
    <p><strong>Time:</strong> <?php echo htmlspecialchars($showtime['show_time']); ?></p>
    <p><strong>Duration:</strong> <?php echo $showtime['duration']; ?> mins</p>
    <p><strong>Rating:</strong> <?php echo htmlspecialchars($showtime['rating']); ?></p>
    <p><strong>Available Seats:</strong> <?php echo $showtime['available_seats']; ?></p>
    <div style="clear: both;"></div>

    <?php if ($success): ?>
        <div style="padding: 10px; background-color: #d4edda; color: #155724; margin-bottom: 15px;">
            <?php echo $success; ?>
        </div>
        <a href="bookings.php">View My Bookings</a>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div style="padding: 10px; background-color: #f8d7da; color: #721c24; margin-bottom: 15px;">
                <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="tickets">Number of Tickets:</label><br>
            <input type="number" id="tickets" name="tickets" min="1" max="<?php echo $showtime['available_seats']; ?>" value="1" required><br><br>

            <p><strong>Price per ticket:</strong> $<?php echo $ticket_price; ?></p>
            <p><strong>Total:</strong> $<span id="total"><?php echo $ticket_price; ?></span></p>

            <button type="submit">Confirm Booking</button>
        </form>
    <?php endif; ?>
</div>

<script>
// Calculate total price live
const ticketsInput = document.getElementById('tickets');
const totalSpan = document.getElementById('total');
const pricePerTicket = <?php echo $ticket_price; ?>;

ticketsInput.addEventListener('input', () => {
    let count = parseInt(ticketsInput.value) || 0;
    if (count < 1) count = 1;
    if (count > <?php echo $showtime['available_seats']; ?>) count = <?php echo $showtime['available_seats']; ?>;
    ticketsInput.value = count;
    totalSpan.textContent = (count * pricePerTicket).toFixed(2);
});
</script>

<?php include 'footer.php'; ?>

</body>
</html>


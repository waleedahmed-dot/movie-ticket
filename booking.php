<?php
session_start();
require_once 'db.php';

$showtime_id = (int)($_GET['id'] ?? 0);
$errors = [];

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch showtime details
$stmt = $pdo->prepare("
    SELECT s.*, m.title 
    FROM showtimes s 
    JOIN movies m ON s.movie_id = m.id 
    WHERE s.id = ?
");
$stmt->execute([$showtime_id]);
$show = $stmt->fetch();

if (!$show) {
    echo "Showtime not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seats = (int)($_POST['seats'] ?? 0);

    if ($seats <= 0 || $seats > $show['available_seats']) {
        $errors[] = "Invalid seat count.";
    }

    if (empty($errors)) {
        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, showtime_id, seats) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $showtime_id, $seats]);

        // Update available seats
        $stmt = $pdo->prepare("UPDATE showtimes SET available_seats = available_seats - ? WHERE id = ?");
        $stmt->execute([$seats, $showtime_id]);

        header("Location: bookings.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Book Ticket for: <?= htmlspecialchars($show['title']) ?></h2>
    <p><strong>Date:</strong> <?= $show['show_date'] ?> | <strong>Time:</strong> <?= $show['show_time'] ?></p>
    <p><strong>Available Seats:</strong> <?= $show['available_seats'] ?></p>

    <?php if ($errors): ?>
        <div style="color:red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Number of Seats:</label><br>
        <input type="number" name="seats" min="1" max="<?= $show['available_seats'] ?>" required><br><br>

        <button type="submit">Confirm Booking</button>
    </form>

    <p><a href="index.php">Back to Home</a></p>
</div>

</body>
</html>

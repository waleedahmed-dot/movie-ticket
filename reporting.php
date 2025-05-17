<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Total bookings
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Total seats sold
$totalSeats = $pdo->query("SELECT SUM(num_seats) FROM bookings")->fetchColumn();

// Total revenue (assuming $10 per seat)
$revenue = $totalSeats * 10;

// Top 5 most booked movies
$sql = "
    SELECT m.title, SUM(b.num_seats) AS total_sold
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.id
    JOIN movies m ON s.movie_id = m.id
    GROUP BY m.title
    ORDER BY total_sold DESC
    LIMIT 5
";
$topMovies = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reporting & Analytics</title>
</head>
<body>
    <h2>📊 Reporting & Analytics</h2>
    <a href="dashboard.php">← Back to Dashboard</a><br><br>

    <p><strong>Total Bookings:</strong> <?= $totalBookings ?></p>
    <p><strong>Total Seats Sold:</strong> <?= $totalSeats ?></p>
    <p><strongEstimated Revenue ($10/seat):</strong> $<?= number_format($revenue, 2) ?></p>

    <h3>Top 5 Booked Movies</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>Movie</th>
            <th>Seats Booked</th>
        </tr>
        <?php foreach ($topMovies as $movie): ?>
            <tr>
                <td><?= htmlspecialchars($movie['title']) ?></td>
                <td><?= $movie['total_sold'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

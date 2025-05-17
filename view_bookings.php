<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$sql = "SELECT b.id AS booking_id, u.name AS user_name, u.email, m.title, s.show_date, s.show_time, b.num_seats, b.booking_date
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN showtimes s ON b.showtime_id = s.id
        JOIN movies m ON s.movie_id = m.id
        ORDER BY b.booking_date DESC";

$stmt = $pdo->query($sql);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>All User Bookings</h2>
    <a href="dashboard.php">← Back to Dashboard</a>

    <table border="1" cellpadding="8">
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Email</th>
            <th>Movie</th>
            <th>Date</th>
            <th>Time</th>
            <th>Seats</th>
            <th>Booked On</th>
        </tr>
        <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= $b['booking_id'] ?></td>
                <td><?= htmlspecialchars($b['user_name']) ?></td>
                <td><?= htmlspecialchars($b['email']) ?></td>
                <td><?= htmlspecialchars($b['title']) ?></td>
                <td><?= $b['show_date'] ?></td>
                <td><?= $b['show_time'] ?></td>
                <td><?= $b['num_seats'] ?></td>
                <td><?= $b['booking_date'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

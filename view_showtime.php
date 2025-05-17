<?php
session_start();
require_once 'db.php';

// Fetch all showtimes with movie info
$stmt = $pdo->query("
    SELECT s.id, s.movie_id, s.show_date, s.show_time, s.available_seats, m.title 
    FROM showtimes s 
    JOIN movies m ON s.movie_id = m.id 
    ORDER BY s.show_date, s.show_time
");
$showtimes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Showtimes - CineReserve</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div style="max-width: 900px; margin: auto; padding: 20px;">
    <h2>All Showtimes</h2>

    <?php if (count($showtimes) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>Movie Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Available Seats</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($showtimes as $show): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($show['title']); ?></td>
                        <td><?php echo htmlspecialchars($show['show_date']); ?></td>
                        <td><?php echo htmlspecialchars($show['show_time']); ?></td>
                        <td><?php echo $show['available_seats']; ?></td>
                        <td><a href="details.php?id=<?php echo $show['movie_id']; ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No showtimes available.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

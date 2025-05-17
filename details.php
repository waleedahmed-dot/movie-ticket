<?php
session_start();
require_once 'db.php';

// Get movie ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie ID.");
}

$movie_id = (int)$_GET['id'];

// Fetch movie details
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    die("Movie not found.");
}

// Fetch showtimes for this movie
$stmt2 = $pdo->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY show_date, show_time");
$stmt2->execute([$movie_id]);
$showtimes = $stmt2->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($movie['title']); ?> - Showtimes | CineReserve</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="movie-details" style="max-width: 700px; margin: auto; padding: 20px;">
    <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
    <img src="images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" width="300" style="float:left; margin-right: 20px;">
    <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
    <p><strong>Duration:</strong> <?php echo $movie['duration']; ?> mins</p>
    <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie['rating']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
    <div style="clear:both;"></div>

    <h3>Available Showtimes</h3>

    <?php if (count($showtimes) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Available Seats</th>
                    <th>Book Now</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($showtimes as $show): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($show['show_date']); ?></td>
                        <td><?php echo htmlspecialchars($show['show_time']); ?></td>
                        <td><?php echo $show['available_seats']; ?></td>
                        <td>
                            <?php if ($show['available_seats'] > 0): ?>
                                <a href="../booking/book.php?showtime_id=<?php echo $show['id']; ?>">Book Tickets</a>
                            <?php else: ?>
                                Sold Out
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No showtimes available for this movie currently.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

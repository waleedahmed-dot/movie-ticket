<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$movie_id = (int)($_GET['movie_id'] ?? 0);

if ($movie_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Fetch movie info
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    header("Location: dashboard.php");
    exit;
}

// Fetch showtimes for this movie
$stmt = $pdo->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY show_date, show_time");
$stmt->execute([$movie_id]);
$showtimes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Showtimes - <?php echo htmlspecialchars($movie['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="max-width: 800px; margin: auto; padding: 20px;">
    <h2>Showtimes for "<?php echo htmlspecialchars($movie['title']); ?>"</h2>

    <a href="add_showtime.php?movie_id=<?php echo $movie_id; ?>">Add New Showtime</a> | 
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if (count($showtimes) === 0): ?>
        <p>No showtimes found.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Show Date</th>
                    <th>Show Time</th>
                    <th>Available Seats</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($showtimes as $st): ?>
                    <tr>
                        <td><?php echo $st['id']; ?></td>
                        <td><?php echo $st['show_date']; ?></td>
                        <td><?php echo $st['show_time']; ?></td>
                        <td><?php echo $st['available_seats']; ?></td>
                        <td>
                            <a href="edit_showtime.php?id=<?php echo $st['id']; ?>&movie_id=<?php echo $movie_id; ?>">Edit</a> |
                            <a href="delete_showtime.php?id=<?php echo $st['id']; ?>&movie_id=<?php echo $movie_id; ?>" onclick="return confirm('Delete this showtime?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

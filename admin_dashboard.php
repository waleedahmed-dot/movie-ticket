<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch all movies
$stmt = $pdo->query("SELECT * FROM movies ORDER BY id DESC");
$movies = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - CineReserve</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="max-width: 900px; margin: auto; padding: 20px;">
    <h2>Admin Dashboard</h2>
    <a href="logout.php">Logout</a> | <a href="add_movie.php">Add New Movie</a>

    <h3>Movies</h3>
    <?php if (count($movies) === 0): ?>
        <p>No movies found.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Duration (min)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $m): ?>
                    <tr>
                        <td><?php echo $m['id']; ?></td>
                        <td><?php echo htmlspecialchars($m['title']); ?></td>
                        <td><?php echo htmlspecialchars($m['description']); ?></td>
                        <td><?php echo $m['duration']; ?></td>
                        <td>
                            <a href="edit_movie.php?id=<?php echo $m['id']; ?>">Edit</a> |
                            <a href="delete_movie.php?id=<?php echo $m['id']; ?>" onclick="return confirm('Delete this movie?');">Delete</a> |
                            <a href="showtimes.php?movie_id=<?php echo $m['id']; ?>">Manage Showtimes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

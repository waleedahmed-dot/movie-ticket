<?php
session_start();
require_once 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Movies - CineReserve</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<h2 style="text-align:center; margin-top: 20px;">All Movies</h2>

<div class="movie-grid">
<?php
$stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
while ($movie = $stmt->fetch()) {
    echo '<div class="movie-card">';
    echo '<img src="../assets/images/' . htmlspecialchars($movie['poster']) . '" alt="' . htmlspecialchars($movie['title']) . '" width="200">';
    echo '<h3>' . htmlspecialchars($movie['title']) . '</h3>';
    echo '<p><strong>Genre:</strong> ' . htmlspecialchars($movie['genre']) . '</p>';
    echo '<p><strong>Duration:</strong> ' . $movie['duration'] . ' mins</p>';
    echo '<p><strong>Rating:</strong> ' . htmlspecialchars($movie['rating']) . '</p>';
    echo '<p>' . nl2br(htmlspecialchars($movie['description'])) . '</p>';
    echo '<p><a href="details.php?id=' . $movie['id'] . '">View Showtimes & Book</a></p>';
    echo '</div>';
}
?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

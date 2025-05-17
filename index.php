<?php
session_start();
require_once 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>CineReserve - Book Movie Tickets Online</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<a href="search.php">Search Movies</a>
<div class="hero">
    <h1>Welcome to CineReserve</h1>
    <p>Book your favorite movie tickets online — fast and easy!</p>
    <a href="login.php">Login</a> 
    <a href="signup.php">Sign Up</a>
</div>

<hr>

<h2 style="text-align:center;">🎥 Now Showing</h2>

<div class="movie-grid">
<?php
$stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 4");
while ($movie = $stmt->fetch()) {
    echo '<div class="movie-card">';
    echo '<img src="assets/images/' . $movie['poster'] . '" width="200">';
    echo '<h3>' . htmlspecialchars($movie['title']) . '</h3>';
    echo '<p>Genre: ' . $movie['genre'] . '</p>';
echo '<p><a href="view_showtime.php?id=' . $movie['id'] . '">View Showtimes</a></p>';
    echo '</div>';
}
?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

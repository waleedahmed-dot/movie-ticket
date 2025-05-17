<?php
require_once 'db.php';

$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';
$date = $_GET['date'] ?? '';

$sql = "SELECT m.*, s.show_date, s.show_time, s.id AS show_id 
        FROM movies m 
        JOIN showtimes s ON m.id = s.movie_id 
        WHERE 1=1";

$params = [];

if ($search) {
    $sql .= " AND m.title LIKE ?";
    $params[] = "%$search%";
}

if ($genre) {
    $sql .= " AND m.genre = ?";
    $params[] = $genre;
}

if ($date) {
    $sql .= " AND s.show_date = ?";
    $params[] = $date;
}

$sql .= " ORDER BY s.show_date ASC, s.show_time ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Movies</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Search & Filter Movies</h2>

    <form method="get">
        <input type="text" name="search" placeholder="Movie Title" value="<?= htmlspecialchars($search) ?>">
        <select name="genre">
            <option value="">All Genres</option>
            <option <?= $genre === 'Action' ? 'selected' : '' ?>>Action</option>
            <option <?= $genre === 'Comedy' ? 'selected' : '' ?>>Comedy</option>
            <option <?= $genre === 'Drama' ? 'selected' : '' ?>>Drama</option>
            <option <?= $genre === 'Horror' ? 'selected' : '' ?>>Horror</option>
        </select>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($results): ?>
        <table border="1" cellpadding="8" width="100%">
            <tr>
                <th>Title</th>
                <th>Genre</th>
                <th>Date</th>
                <th>Time</th>
                <th>Book</th>
            </tr>
            <?php foreach ($results as $movie): ?>
                <tr>
                    <td><?= htmlspecialchars($movie['title']) ?></td>
                    <td><?= htmlspecialchars($movie['genre']) ?></td>
                    <td><?= $movie['show_date'] ?></td>
                    <td><?= $movie['show_time'] ?></td>
                    <td><a href="booking.php?id=<?= $movie['show_id'] ?>">Book</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No movies found.</p>
    <?php endif; ?>
</div>

</body>
</html>

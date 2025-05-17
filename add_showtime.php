<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$movie_id = (int)($_GET['movie_id'] ?? 0);
if ($movie_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $show_date = $_POST['show_date'] ?? '';
    $show_time = $_POST['show_time'] ?? '';
    $available_seats = (int)($_POST['available_seats'] ?? 0);

    if (!$show_date || !$show_time || $available_seats <= 0) {
        $errors[] = "All fields are required and seats must be a positive number.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO showtimes (movie_id, show_date, show_time, available_seats) VALUES (?, ?, ?, ?)");
        $stmt->execute([$movie_id, $show_date, $show_time, $available_seats]);
        header("Location: showtimes.php?movie_id=$movie_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Showtime</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h2>Add Showtime</h2>

    <?php if ($errors): ?>
        <div style="color: red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Show Date</label><br>
        <input type="date" name="show_date" required><br><br>

        <label>Show Time</label><br>
        <input type="time" name="show_time" required><br><br>

        <label>Available Seats</label><br>
        <input type="number" name="available_seats" min="1" required><br><br>

        <button type="submit">Add Showtime</button>
    </form>

    <p><a href="showtimes.php?movie_id=<?php echo $movie_id; ?>">Back to Showtimes</a></p>
</div>

</body>
</html>

<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$showtime_id = (int)($_GET['id'] ?? 0);
$movie_id = (int)($_GET['movie_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM showtimes WHERE id = ?");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

if (!$showtime) {
    header("Location: showtimes.php?movie_id=$movie_id");
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
        $stmt = $pdo->prepare("UPDATE showtimes SET show_date = ?, show_time = ?, available_seats = ? WHERE id = ?");
        $stmt->execute([$show_date, $show_time, $available_seats, $showtime_id]);
        header("Location: showtimes.php?movie_id=$movie_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Showtime</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h2>Edit Showtime</h2>

    <?php if ($errors): ?>
        <div style="color: red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Show Date</label><br>
        <input type="date" name="show_date" value="<?php echo $showtime['show_date']; ?>" required><br><br>

        <label>Show Time</label><br>
        <input type="time" name="show_time" value="<?php echo $showtime['show_time']; ?>" required><br><br>

        <label>Available Seats</label><br>
        <input type="number" name="available_seats" value="<?php echo $showtime['available_seats']; ?>" min="1" required><br><br>

        <button type="submit">Update Showtime</button>
    </form>

    <p><a href="showtimes.php?movie_id=<?php echo $movie_id; ?>">Back to Showtimes</a></p>
</div>

</body>
</html>

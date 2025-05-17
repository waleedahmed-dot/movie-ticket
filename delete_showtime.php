<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$showtime_id = (int)($_GET['id'] ?? 0);
$movie_id = (int)($_GET['movie_id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM showtimes WHERE id = ?");
$stmt->execute([$showtime_id]);

header("Location: showtimes.php?movie_id=$movie_id");
exit;
?>

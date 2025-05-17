<?php
// config/db.php

$host = 'localhost';
$db   = 'movie_ticket';
$user = 'root';      // your MySQL username
$pass = 'password';          // your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=3307;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("Database Connection Failed why   hhjh: " . $e->getMessage());
}
?>

<?php
session_start();
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // For simplicity, hardcoded admin credentials
    $adminUser = 'admin';
    $adminPass = '123456'; // In real apps, hash this and store in DB

    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $errors[] = "Invalid admin username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - CineReserve</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="max-width: 400px; margin: auto; padding: 20px;">
    <h2>Admin Login</h2>

    <?php if ($errors): ?>
        <div style="color: red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Username</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Log In</button>
    </form>
</div>

</body>
</html>

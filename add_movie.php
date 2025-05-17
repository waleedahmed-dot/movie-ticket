<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);

    if (empty($title)) $errors[] = "Title is required.";
    if ($duration <= 0) $errors[] = "Duration must be a positive number.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO movies (title, description, duration) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $duration]);
        header("Location: dashboard.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Movie - CineReserve</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h2>Add New Movie</h2>

    <?php if ($errors): ?>
        <div style="color: red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Title</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? '') ?>" required><br><br>

        <label>Description</label><br>
        <textarea name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? '') ?></textarea><br><br>

        <label>Duration (minutes)</label><br>
        <input type="number" name="duration" min="1" value="<?php echo htmlspecialchars($_POST['duration'] ?? '') ?>" required><br><br>

        <button type="submit">Add Movie</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>

</body>
</html>

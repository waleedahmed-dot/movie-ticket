<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db.php';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $percent = intval($_POST['percent']);
    $expiry = $_POST['expiry'];

    $stmt = $pdo->prepare("INSERT INTO discounts (code, discount_percent, expiry_date) VALUES (?, ?, ?)");
    $stmt->execute([$code, $percent, $expiry]);
}

// Delete promo
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM discounts WHERE id = ?")->execute([$id]);
}

// Fetch all discounts
$discounts = $pdo->query("SELECT * FROM discounts ORDER BY expiry_date")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Discounts</title>
</head>
<body>
    <h2>🎁 Promotions & Discounts</h2>
    <a href="dashboard.php">← Back to Dashboard</a><br><br>

    <form method="POST">
        <input type="text" name="code" placeholder="Promo Code (e.g. SAVE10)" required>
        <input type="number" name="percent" placeholder="Discount (%)" min="1" max="100" required>
        <input type="date" name="expiry" required>
        <button type="submit">Add Discount</button>
    </form>

    <h3>All Discounts</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>Code</th>
            <th>Discount %</th>
            <th>Expires</th>
            <th>Action</th>
        </tr>
        <?php foreach ($discounts as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['code']) ?></td>
                <td><?= $d['discount_percent'] ?>%</td>
                <td><?= $d['expiry_date'] ?></td>
                <td><a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Delete this promo?')">❌ Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

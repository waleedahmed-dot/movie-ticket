<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Reply to ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['ticket_id'];
    $reply = $_POST['reply'];
    $stmt = $pdo->prepare("UPDATE support_tickets SET reply = ?, status = 'Resolved' WHERE id = ?");
    $stmt->execute([$reply, $id]);
}

// Get all tickets
$tickets = $pdo->query("
    SELECT s.*, u.username FROM support_tickets s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Admin Support</title></head>
<body>
    <h2>🛠️ Admin Support Tickets</h2>
    <a href="dashboard.php">← Back to Dashboard</a><br><br>

    <?php foreach ($tickets as $t): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <strong>User:</strong> <?= htmlspecialchars($t['username']) ?><br>
            <strong>Subject:</strong> <?= htmlspecialchars($t['subject']) ?><br>
            <strong>Message:</strong> <?= nl2br(htmlspecialchars($t['message'])) ?><br>
            <strong>Status:</strong> <?= $t['status'] ?><br>
            <strong>Reply:</strong><br>
            <form method="POST">
                <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                <textarea name="reply" rows="3" required><?= htmlspecialchars($t['reply']) ?></textarea><br>
                <button type="submit">Send Reply</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>

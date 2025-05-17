<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO support_tickets (user_id, subject, message) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $subject, $message]);
}

// Fetch user's tickets
$stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Support</title></head>
<body>
    <h2>🛠️ Support & Help Center</h2>
    <a href="profile.php">← Back to Profile</a><br><br>

    <form method="POST">
        <input type="text" name="subject" placeholder="Subject" required><br>
        <textarea name="message" placeholder="Describe your issue..." rows="5" required></textarea><br>
        <button type="submit">Submit Ticket</button>
    </form>

    <h3>📋 Your Tickets</h3>
    <?php foreach ($tickets as $ticket): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <strong>Subject:</strong> <?= htmlspecialchars($ticket['subject']) ?><br>
            <strong>Status:</strong> <?= $ticket['status'] ?><br>
            <strong>Message:</strong> <?= nl2br(htmlspecialchars($ticket['message'])) ?><br>
            <?php if ($ticket['reply']): ?>
                <strong>Reply:</strong> <?= nl2br(htmlspecialchars($ticket['reply'])) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>

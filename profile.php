<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Fetch user info
$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}

// Update profile form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');

    if (empty($new_username)) $errors[] = "Username cannot be empty.";
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email.";

    // Check if username or email already taken by others
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$new_username, $new_email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already in use by another account.";
        }
    }

    // If no errors, update database
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$new_username, $new_email, $user_id])) {
            $success = "Profile updated successfully.";
            $_SESSION['username'] = $new_username; // update session username
            $user['username'] = $new_username;
            $user['email'] = $new_email;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Profile - CineReserve</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div style="max-width: 500px; margin: auto; padding: 20px;">
    <h2>Your Profile</h2>

    <?php if ($success): ?>
        <div style="color: green; margin-bottom: 15px;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Username</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

        <label>Email</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>Member since</label><br>
        <input type="text" value="<?= htmlspecialchars($user['created_at']) ?>" disabled><br><br>

        <button type="submit">Update Profile</button>
    </form>

    <p><a href="list.php">Back to Movie Listings</a></p>
</div>
</body>
</html>

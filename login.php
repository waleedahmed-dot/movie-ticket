<?php
session_start();
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("location: list.php");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log In - CineReserve</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="max-width: 400px; margin: auto; padding: 20px;">
    <h2>Log In</h2>

    <?php if (!empty($errors)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Email</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Log In</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</div>

</body>
</html>

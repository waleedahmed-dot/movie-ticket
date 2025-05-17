<nav>
    <div class="nav-wrapper">
        <a href="index.php">CineReserve</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">My Profile</a>
                <a href="bookings.php">My Bookings</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

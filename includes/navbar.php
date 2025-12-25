<nav>
    <a href="index.php" class="logo" style="text-decoration: none; color: #fff;"><span>●</span> QuickNote</a>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn-cta-nav">Dashboard</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" class="btn-cta-nav">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>
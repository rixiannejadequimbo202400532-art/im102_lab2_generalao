<?php
// navbar.php - Shared navigation bar
// Requires session_start() and $pdo from config.php
?>
<nav class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="navbar-brand">
            <span>🗄️</span> IM102 Lab 3
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <ul class="nav-links">
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">Reports</a></li>

                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="add.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add.php' ? 'active' : ''; ?>">Add User</a></li>
                <?php endif; ?>

                <li>
                    <span class="nav-user">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <span class="nav-role"><?php echo $_SESSION['user_role']; ?></span>
                    </span>
                </li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        <?php else: ?>
            <ul class="nav-links">
                <li><a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a></li>
                <li><a href="register.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">Register</a></li>
            </ul>
        <?php endif; ?>
    </div>
</nav>
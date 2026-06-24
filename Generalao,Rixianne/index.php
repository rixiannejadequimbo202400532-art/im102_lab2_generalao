<?php
require_once 'config.php';
require_once 'auth.php';

requireAuth();

// Fetch all users for display
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Count stats
$totalUsers = count($users);
$adminCount = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
$staffCount = $totalUsers - $adminCount;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IM102 Lab 3</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Manage users and view system overview</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="value"><?php echo $totalUsers; ?></div>
                <div class="change">All registered accounts</div>
            </div>
            <div class="stat-card">
                <h3>Admins</h3>
                <div class="value"><?php echo $adminCount; ?></div>
                <div class="change">Full system access</div>
            </div>
            <div class="stat-card">
                <h3>Staff</h3>
                <div class="value"><?php echo $staffCount; ?></div>
                <div class="change">Standard users</div>
            </div>
        </div>

        <div class="card">
            <div class="page-header" style="margin-bottom: 20px;">
                <h1>All Users</h1>
                <?php if (isAdmin()): ?>
                    <a href="add.php" class="btn btn-success btn-sm">+ Add New User</a>
                <?php endif; ?>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <h3>No users found</h3>
                    <p>Get started by adding a new user.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <?php if (isAdmin()): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['role']; ?>">
                                            <?php echo $user['role']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <?php if (isAdmin()): ?>
                                        <td class="actions">
                                            <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                <a href="delete.php?id=<?php echo $user['id']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                    Delete
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
require_once 'config.php';
require_once 'auth.php';

requireAuth();

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Calculate stats
$totalUsers = count($users);
$adminCount = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
$staffCount = $totalUsers - $adminCount;

// Recent registrations (last 7 days)
$recentStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$recentStmt->execute();
$recentCount = $recentStmt->fetchColumn();

// Oldest account
$oldestStmt = $pdo->query("SELECT MIN(created_at) as oldest FROM users");
$oldestDate = $oldestStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - IM102 Lab 3</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>System Reports</h1>
            <p>User statistics and analytics overview</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="value"><?php echo $totalUsers; ?></div>
                <div class="change">Registered accounts</div>
            </div>
            <div class="stat-card">
                <h3>Administrators</h3>
                <div class="value"><?php echo $adminCount; ?></div>
                <div class="change"><?php echo $totalUsers > 0 ? round(($adminCount / $totalUsers) * 100, 1) : 0; ?>% of total</div>
            </div>
            <div class="stat-card">
                <h3>Staff Members</h3>
                <div class="value"><?php echo $staffCount; ?></div>
                <div class="change"><?php echo $totalUsers > 0 ? round(($staffCount / $totalUsers) * 100, 1) : 0; ?>% of total</div>
            </div>
            <div class="stat-card">
                <h3>New This Week</h3>
                <div class="value"><?php echo $recentCount; ?></div>
                <div class="change">Last 7 days</div>
            </div>
        </div>

        <div class="card">
            <div class="page-header" style="margin-bottom: 20px;">
                <h1>User Directory</h1>
                <p>Complete list of all registered users</p>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Account Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user):
                            $created = new DateTime($user['created_at']);
                            $now = new DateTime();
                            $age = $created->diff($now);
                        ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td><?php echo $created->format('M d, Y H:i'); ?></td>
                                <td>
                                    <?php
                                    if ($age->y > 0) echo $age->y . ' year' . ($age->y > 1 ? 's' : '');
                                    elseif ($age->m > 0) echo $age->m . ' month' . ($age->m > 1 ? 's' : '');
                                    elseif ($age->d > 0) echo $age->d . ' day' . ($age->d > 1 ? 's' : '');
                                    else echo 'Today';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top: 24px;">
            <div class="page-header" style="margin-bottom: 20px;">
                <h1>System Info</h1>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <tbody>
                        <tr>
                            <td><strong>First Account Created</strong></td>
                            <td><?php echo $oldestDate ? date('F d, Y', strtotime($oldestDate)) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database Name</strong></td>
                            <td>im102_lab3</td>
                        </tr>
                        <tr>
                            <td><strong>Password Hashing</strong></td>
                            <td>bcrypt (PASSWORD_DEFAULT)</td>
                        </tr>
                        <tr>
                            <td><strong>Session Status</strong></td>
                            <td>Active (<?php echo htmlspecialchars($_SESSION['username']); ?>)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
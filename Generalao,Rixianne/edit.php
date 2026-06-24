<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$id = $_GET['id'] ?? 0;
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php?error=notfound');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'staff';
    $new_password = $_POST['new_password'] ?? '';

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    }

    if (!empty($username) && (strlen($username) < 3 || strlen($username) > 50)) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }

    if (!empty($username) && !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (!empty($new_password) && strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters long.";
    }

    if (!in_array($role, ['admin', 'staff'])) {
        $errors[] = "Invalid role selected.";
    }

    // Check duplicate username (excluding current user)
    if (empty($errors) && !empty($username) && $username !== $user['username']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $id]);
        if ($stmt->fetch()) {
            $errors[] = "Username '$username' is already taken.";
        }
    }

    // Check duplicate email (excluding current user)
    if (empty($errors) && !empty($email) && $email !== $user['email']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $errors[] = "Email '$email' is already registered.";
        }
    }

    if (empty($errors)) {
        try {
            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, password_hash = ?, role = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$username, $email, $password_hash, $role, $id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, role = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$username, $email, $role, $id]);
            }

            header('Location: index.php?updated=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Failed to update user. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - IM102 Lab 3</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Edit User</h1>
            <p>Update user #<?php echo $user['id']; ?> information</p>
        </div>

        <div class="card card-sm">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Please fix the following:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" novalidate>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password (leave blank to keep current)</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password (min 6 chars)">
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success btn-block">Update User</button>
                <a href="index.php" class="btn btn-primary btn-block" style="margin-top: 12px; background: var(--gray);">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>
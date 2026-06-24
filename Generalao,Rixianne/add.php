<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$errors = [];
$success = false;

$old = [
    'username' => '',
    'email' => '',
    'role' => 'staff'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'staff';

    $old['username'] = htmlspecialchars($username);
    $old['email'] = htmlspecialchars($email);
    $old['role'] = $role;

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
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

    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (!in_array($role, ['admin', 'staff'])) {
        $errors[] = "Invalid role selected.";
    }

    if (empty($errors) && !empty($username)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username '$username' is already taken.";
        }
    }

    if (empty($errors) && !empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email '$email' is already registered.";
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role) 
            VALUES (?, ?, ?, ?)
        ");

        try {
            $stmt->execute([$username, $email, $password_hash, $role]);
            header('Location: index.php?added=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Failed to add user. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - IM102 Lab 3</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Add New User</h1>
            <p>Create a new user account</p>
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
                        value="<?php echo $old['username']; ?>"
                        placeholder="Choose a username">
                    <p class="hint">Letters, numbers, and underscores only (3-50 chars)</p>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo $old['email']; ?>"
                        placeholder="user@email.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimum 6 characters">
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="staff" <?php echo $old['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="admin" <?php echo $old['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success btn-block">Add User</button>
                <a href="index.php" class="btn btn-primary btn-block" style="margin-top: 12px; background: var(--gray);">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>
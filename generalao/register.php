<?php
require_once 'config.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Username validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username cannot exceed 50 characters.";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Confirm password
    if (empty($confirmPassword)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check duplicates
    if (empty($errors)) {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT username, email
             FROM users
             WHERE username = ? OR email = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $username,
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {

            if ($row['username'] === $username) {
                $errors[] = "Username already exists.";
            }

            if ($row['email'] === $email) {
                $errors[] = "Email already exists.";
            }
        }
    }

    // Insert user
    if (empty($errors)) {

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users
            (username, email, password_hash)
            VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $username,
            $email,
            $passwordHash
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Registration successful!";
        } else {
            $errors[] = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>User Registration</h2>

<?php if (!empty($success)): ?>
    <p style="color: green;">
        <?= htmlspecialchars($success) ?>
    </p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div style="color: red;">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST">

    <label>Username</label><br>
    <input
        type="text"
        name="username"
        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
    >
    <br><br>

    <label>Email</label><br>
    <input
        type="email"
        name="email"
        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
    >
    <br><br>

    <label>Password</label><br>
    <input
        type="password"
        name="password"
    >
    <br><br>

    <label>Confirm Password</label><br>
    <input
        type="password"
        name="confirm_password"
    >
    <br><br>

    <button type="submit">
        Register
    </button>

</form>

</body>
</html>
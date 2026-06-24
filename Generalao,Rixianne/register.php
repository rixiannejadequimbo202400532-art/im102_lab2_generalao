<?php
// register.php - User registration with full validation
require_once 'config.php';

$errors = [];
$success = false;

$old = [
    'username' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $old['username'] = htmlspecialchars($username);
    $old['email'] = htmlspecialchars($email);

    // 1. Empty fields check
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password.";
    }

    // 2. Username length (3-50 chars)
    if (!empty($username) && (strlen($username) < 3 || strlen($username) > 50)) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }

    // 3. Username format (alphanumeric + underscore only)
    if (!empty($username) && !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }

    // 4. Email format validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // 5. Password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // 6. Password length (minimum 6 characters)
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // 7. Check if username already exists
    if (empty($errors) && !empty($username)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username '$username' is already taken. Please choose another.";
        }
    }

    // 8. Check if email already exists
    if (empty($errors) && !empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email '$email' is already registered. Please use a different email or log in.";
        }
    }

    // Insert if valid
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role) 
            VALUES (?, ?, ?, 'staff')
        ");

        try {
            $stmt->execute([$username, $email, $password_hash]);
            $success = true;
            $old = ['username' => '', 'email' => ''];
        } catch (PDOException $e) {
            $errors[] = "Registration failed. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IM102 Lab 3</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
        }

        h1 {
            color: #333;
            margin-bottom: 8px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #444;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        input.error {
            border-color: #e74c3c;
            background-color: #fdf2f2;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .error-box {
            background: #fdf2f2;
            border-left: 4px solid #e74c3c;
            padding: 16px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error-box h3 {
            color: #c0392b;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .error-box ul {
            list-style: none;
            padding-left: 0;
        }

        .error-box li {
            color: #e74c3c;
            font-size: 14px;
            padding: 4px 0;
            padding-left: 20px;
            position: relative;
        }

        .error-box li::before {
            content: "";
            position: absolute;
            left: 0;
        }

        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 16px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #155724;
        }

        .success-box h3 {
            margin-bottom: 8px;
        }

        .hint {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create Account</h1>
        <p class="subtitle">IM102 Lab 3 — User Registration</p>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <h3>Please fix the following:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-box">
                <h3>✓ Registration Successful!</h3>
                <p>Your account has been created. You can now log in.</p>
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
                    placeholder="Choose a username (3-50 chars)">
                <p class="hint">Letters, numbers, and underscores only</p>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo $old['email']; ?>"
                    placeholder="your@email.com">
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
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password">
            </div>

            <button type="submit">Register</button>
        </form>

        <p class="login-link">
            Already have an account? <a href="#">Log in</a>
        </p>
    </div>
</body>

</html>
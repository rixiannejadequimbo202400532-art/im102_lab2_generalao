<?php
// auth.php - Authentication Functions
require_once 'config.php';

// Register a new user
function registerUser($fullName, $email, $username, $password, $role = 'staff', $phone = '') {
    global $pdo;
    
    // Check if username or email exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password_hash, role, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullName, $email, $username, $passwordHash, $role, $phone]);
    
    return ['success' => true, 'message' => 'Registration successful!'];
}

// Login user
function loginUser($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        return ['success' => true, 'message' => 'Login successful!'];
    }
    
    return ['success' => false, 'message' => 'Invalid username or password'];
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
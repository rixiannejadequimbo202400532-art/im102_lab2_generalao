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

// Prevent self-deletion
if ($id === $_SESSION['user_id']) {
    header('Location: index.php?error=selfdelete');
    exit;
}

// Verify user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: index.php?error=notfound');
    exit;
}

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php?deleted=1');
exit;

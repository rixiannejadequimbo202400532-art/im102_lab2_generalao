<?php
// logout.php - Destroy session and redirect

require_once 'config.php';

// Clear all session data
$_SESSION = [];

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: login.php?loggedout=1');
exit;

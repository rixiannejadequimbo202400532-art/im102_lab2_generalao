<?php
// auth.php - Authentication helper functions
// Include this in pages that require login

function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin()
{
    requireAuth();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: index.php?error=unauthorized');
        exit;
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

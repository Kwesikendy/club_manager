<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect non-logged-in users to login page
 */
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: ../members/login.php');
        exit();
    }
}

/**
 * Check if user is admin and redirect if not
 */
function checkAdmin() {
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit();
    }
}

/**
 * Return true if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}
?>

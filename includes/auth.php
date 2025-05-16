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
 * Check if user is admin (optional)
 */
function checkAdmin() {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: ../index.php');
        exit();
    }
}
?>
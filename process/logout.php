<?php
// process/logout.php

// 1. Start session and destroy it
session_start();
session_unset();
session_destroy();

// 2. Clear session cookie
setcookie(session_name(), '', time() - 3600, '/');

// 3. Redirect to homepage
header('Location: ../index.php');
exit();
?>
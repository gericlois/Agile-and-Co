<?php
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']);
require_once '../config/database.php';
require_once '../config/activity-log.php';
if (isset($_SESSION['admin_id'])) {
    logActivity($pdo, 'logout', 'session', 'Admin logged out');
}
session_destroy();
header('Location: login.php');
exit;

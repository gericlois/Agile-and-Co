<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';
logActivity($pdo, 'logout', 'session', 'Admin logged out');
session_destroy();
header('Location: login.php');
exit;

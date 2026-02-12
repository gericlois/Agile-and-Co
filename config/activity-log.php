<?php
function logActivity($pdo, $action, $entityType, $entityLabel = '', $details = null) {
    $adminId = $_SESSION['admin_id'] ?? null;
    $adminUsername = $_SESSION['admin_username'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO activity_log (admin_id, admin_username, action, entity_type, entity_label, details) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$adminId, $adminUsername, $action, $entityType, $entityLabel, $details]);
}

<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../config/database.php';
require_once '../config/lead-scoring.php';
require_once '../config/activity-log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$contactId = (int)($input['contact_id'] ?? 0);

if ($contactId <= 0) {
    echo json_encode(['error' => 'Invalid contact ID']);
    exit;
}

$result = scoreLeadWithAI($pdo, $contactId);

if ($result['error']) {
    echo json_encode(['error' => $result['error']]);
} else {
    logActivity($pdo, 'update', 'contact', 'Scored lead #' . $contactId . ' as ' . $result['score']);
    echo json_encode([
        'score' => $result['score'],
        'reason' => $result['reason']
    ]);
}

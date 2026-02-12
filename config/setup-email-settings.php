<?php
require_once __DIR__ . '/database.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS notification_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert default email if table is empty
$count = $pdo->query("SELECT COUNT(*) FROM notification_emails")->fetchColumn();
if ($count == 0) {
    $pdo->exec("INSERT INTO notification_emails (email) VALUES ('geric@agileandco.com')");
}

echo "notification_emails table ready.";

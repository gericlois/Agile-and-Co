<?php
require_once __DIR__ . '/database.php';

// Add lead_score column (hot, warm, cold, or NULL)
$columns = $pdo->query("SHOW COLUMNS FROM contacts LIKE 'lead_score'")->fetchAll();
if (empty($columns)) {
    $pdo->exec("ALTER TABLE contacts ADD COLUMN lead_score VARCHAR(10) DEFAULT NULL");
    echo "Added lead_score column.\n";
} else {
    echo "lead_score column already exists.\n";
}

// Add lead_score_reason column
$columns = $pdo->query("SHOW COLUMNS FROM contacts LIKE 'lead_score_reason'")->fetchAll();
if (empty($columns)) {
    $pdo->exec("ALTER TABLE contacts ADD COLUMN lead_score_reason TEXT DEFAULT NULL");
    echo "Added lead_score_reason column.\n";
} else {
    echo "lead_score_reason column already exists.\n";
}

// Add lead_scored_at timestamp column
$columns = $pdo->query("SHOW COLUMNS FROM contacts LIKE 'lead_scored_at'")->fetchAll();
if (empty($columns)) {
    $pdo->exec("ALTER TABLE contacts ADD COLUMN lead_scored_at TIMESTAMP NULL DEFAULT NULL");
    echo "Added lead_scored_at column.\n";
} else {
    echo "lead_scored_at column already exists.\n";
}

echo "Lead scoring migration complete.\n";

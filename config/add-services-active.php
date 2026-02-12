<?php
require_once __DIR__ . '/database.php';

$cols = $pdo->query("SHOW COLUMNS FROM services LIKE 'is_active'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE services ADD COLUMN is_active TINYINT(1) DEFAULT 1");
    echo "Added is_active column to services table.\n";
} else {
    echo "is_active column already exists.\n";
}

<?php
require_once __DIR__ . '/database.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS core_pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL,
            label VARCHAR(100) NOT NULL,
            is_custom TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $stmt = $pdo->prepare("INSERT IGNORE INTO core_pages (slug, label, is_custom, sort_order) VALUES (?, ?, 1, ?)");
    $stmt->execute(['main', 'Core', 1]);

    echo "Core pages table created and seeded successfully.\n";

    $count = $pdo->query("SELECT COUNT(*) FROM core_pages")->fetchColumn();
    echo "Total core pages: $count\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

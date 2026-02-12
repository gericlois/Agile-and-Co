<?php
require_once __DIR__ . '/database.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL,
            label VARCHAR(100) NOT NULL,
            is_custom TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $stmt = $pdo->prepare("INSERT IGNORE INTO services (slug, label, is_custom, sort_order) VALUES (?, ?, 1, ?)");
    $stmt->execute(['seo', 'SEO', 1]);
    $stmt->execute(['gads', 'Google Ads', 2]);
    $stmt->execute(['meta', 'Meta Ads', 3]);
    $stmt->execute(['webdesign', 'Web Design', 4]);

    echo "Services table created and seeded successfully.\n";

    $count = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    echo "Total services: $count\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
require_once __DIR__ . '/database.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS industries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL,
            label VARCHAR(100) NOT NULL,
            is_custom TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $stmt = $pdo->prepare("INSERT IGNORE INTO industries (slug, label, is_custom, sort_order) VALUES (?, ?, 1, ?)");
    $stmt->execute(['listing', 'Listing', 1]);
    $stmt->execute(['hvac', 'HVAC', 2]);
    $stmt->execute(['plumbing', 'Plumbing', 3]);
    $stmt->execute(['electrical', 'Electrical', 4]);
    $stmt->execute(['remodeling', 'Remodeling', 5]);
    $stmt->execute(['moving', 'Moving', 6]);
    $stmt->execute(['deck', 'Deck', 7]);
    $stmt->execute(['landscaping', 'Landscaping', 8]);
    $stmt->execute(['gc', 'Gen. Contractors', 9]);
    $stmt->execute(['automotive', 'Automotive', 10]);
    $stmt->execute(['medspa', 'Med Spa', 11]);
    $stmt->execute(['fencing', 'Fencing', 12]);

    echo "Industries table created and seeded successfully.\n";

    $count = $pdo->query("SELECT COUNT(*) FROM industries")->fetchColumn();
    echo "Total industries: $count\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

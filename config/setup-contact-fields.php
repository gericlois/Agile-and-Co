<?php
require_once __DIR__ . '/database.php';

try {
    // Create contact_form_fields table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_form_fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            field_key VARCHAR(50) UNIQUE NOT NULL,
            field_label VARCHAR(100) NOT NULL,
            field_type ENUM('text','email','tel','textarea','select') DEFAULT 'text',
            placeholder VARCHAR(255) DEFAULT '',
            is_required TINYINT(1) DEFAULT 0,
            is_core TINYINT(1) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            field_width ENUM('full','half') DEFAULT 'full',
            sort_order INT DEFAULT 0,
            select_options TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Seed with 7 existing fields
    $stmt = $pdo->prepare("INSERT IGNORE INTO contact_form_fields (field_key, field_label, field_type, placeholder, is_required, is_core, is_active, field_width, sort_order, select_options) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $fields = [
        ['first_name', 'First Name', 'text', 'John', 1, 1, 1, 'half', 1, null],
        ['last_name', 'Last Name', 'text', 'Smith', 1, 1, 1, 'half', 2, null],
        ['email', 'Email', 'email', 'john@company.com', 1, 1, 1, 'full', 3, null],
        ['phone', 'Phone', 'tel', '(555) 123-4567', 0, 0, 1, 'full', 4, null],
        ['company', 'Company', 'text', 'Your Company Name', 0, 0, 1, 'full', 5, null],
        ['industry', 'Industry', 'select', '', 0, 0, 1, 'full', 6, json_encode([
            ['value' => 'hvac', 'label' => 'HVAC'],
            ['value' => 'plumbing', 'label' => 'Plumbing'],
            ['value' => 'electrical', 'label' => 'Electrical'],
            ['value' => 'remodeling', 'label' => 'Home Remodeling'],
            ['value' => 'landscaping', 'label' => 'Landscaping'],
            ['value' => 'other', 'label' => 'Other'],
        ])],
        ['message', 'Message', 'textarea', 'Tell us about your business and what you\'re looking to achieve...', 0, 0, 1, 'full', 7, null],
    ];

    foreach ($fields as $f) {
        $stmt->execute($f);
    }

    // Add custom_fields column to contacts table if it doesn't exist
    $columns = $pdo->query("SHOW COLUMNS FROM contacts LIKE 'custom_fields'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("ALTER TABLE contacts ADD COLUMN custom_fields TEXT DEFAULT NULL");
        echo "Added custom_fields column to contacts table.\n";
    } else {
        echo "custom_fields column already exists.\n";
    }

    echo "Contact form fields table created and seeded successfully.\n";

    $count = $pdo->query("SELECT COUNT(*) FROM contact_form_fields")->fetchColumn();
    echo "Total contact form fields: $count\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

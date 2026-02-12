<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$defaults = require '../config/contact-defaults.php';
$success = '';
$error = '';

// Handle field actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save_content';

    if ($action === 'add_field') {
        $fKey = preg_replace('/[^a-z0-9_]/', '', strtolower(str_replace([' ', '-'], '_', trim($_POST['field_key'] ?? ''))));
        $fLabel = trim($_POST['field_label'] ?? '');
        $fType = $_POST['field_type'] ?? 'text';
        $fPlaceholder = trim($_POST['field_placeholder'] ?? '');
        $fRequired = isset($_POST['field_required']) ? 1 : 0;
        $fWidth = $_POST['field_width'] ?? 'full';

        if ($fKey && $fLabel) {
            $maxOrder = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM contact_form_fields")->fetchColumn();
            $stmt = $pdo->prepare("INSERT INTO contact_form_fields (field_key, field_label, field_type, placeholder, is_required, is_core, is_active, field_width, sort_order) VALUES (?, ?, ?, ?, ?, 0, 1, ?, ?)");
            try {
                $stmt->execute([$fKey, $fLabel, $fType, $fPlaceholder, $fRequired, $fWidth, $maxOrder + 1]);
                logActivity($pdo, 'create', 'field', 'Added contact form field: ' . $fLabel);
                $success = 'Field "' . htmlspecialchars($fLabel) . '" added successfully.';
            } catch (PDOException $e) {
                $error = str_contains($e->getMessage(), 'Duplicate') ? 'A field with that key already exists.' : 'Error adding field.';
            }
        } else {
            $error = 'Field key and label are required.';
        }
    } elseif ($action === 'delete_field') {
        $fId = (int)($_POST['field_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM contact_form_fields WHERE id = ? AND is_core = 0");
        $stmt->execute([$fId]);
        logActivity($pdo, 'delete', 'field', 'Deleted contact form field #' . $fId);
        $success = 'Field deleted successfully.';
    } elseif ($action === 'toggle_field') {
        $fId = (int)($_POST['field_id'] ?? 0);
        $pdo->prepare("UPDATE contact_form_fields SET is_active = 1 - is_active WHERE id = ? AND is_core = 0")->execute([$fId]);
        logActivity($pdo, 'toggle', 'field', 'Toggled contact form field #' . $fId);
        $success = 'Field visibility updated.';
    } elseif ($action === 'update_fields') {
        $fieldIds = $_POST['field_ids'] ?? [];
        foreach ($fieldIds as $fId) {
            $fId = (int)$fId;
            $fLabel = trim($_POST["label_$fId"] ?? '');
            $fPlaceholder = trim($_POST["placeholder_$fId"] ?? '');
            $fRequired = isset($_POST["required_$fId"]) ? 1 : 0;
            $fWidth = $_POST["width_$fId"] ?? 'full';
            $fOrder = (int)($_POST["order_$fId"] ?? 0);

            // Handle select options
            $selectOptions = null;
            if (!empty($_POST["opt_values_$fId"])) {
                $optValues = $_POST["opt_values_$fId"];
                $optLabels = $_POST["opt_labels_$fId"] ?? [];
                $opts = [];
                for ($oi = 0; $oi < count($optValues); $oi++) {
                    $ov = trim($optValues[$oi]);
                    $ol = trim($optLabels[$oi] ?? '');
                    if ($ov !== '' || $ol !== '') {
                        $opts[] = ['value' => $ov, 'label' => $ol];
                    }
                }
                $selectOptions = !empty($opts) ? json_encode($opts) : null;
            }

            $stmt = $pdo->prepare("UPDATE contact_form_fields SET field_label = ?, placeholder = ?, is_required = ?, field_width = ?, sort_order = ?, select_options = COALESCE(?, select_options) WHERE id = ?");
            $stmt->execute([$fLabel, $fPlaceholder, $fRequired, $fWidth, $fOrder, $selectOptions, $fId]);
        }
        logActivity($pdo, 'update', 'field', 'Updated contact form fields');
        $success = 'Form fields updated successfully.';
    } elseif ($action === 'save_content') {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO site_content (section, field_key, field_value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)"
            );
            foreach ($_POST['content'] as $section => $fields) {
                foreach ($fields as $key => $value) {
                    $stmt->execute([$section, $key, trim($value)]);
                }
            }
            logActivity($pdo, 'update', 'content', 'Updated contact page content');
            $success = 'Contact page content saved successfully.';
        } catch (PDOException $e) {
            $error = 'Error saving content. Please try again.';
        }
    }
}

// Load current content
$rows = $pdo->query("SELECT section, field_key, field_value FROM site_content")->fetchAll();
$content = [];
foreach ($rows as $row) {
    $content[$row['section']][$row['field_key']] = $row['field_value'];
}

function sc($content, $section, $key, $default = '') {
    return $content[$section][$key] ?? $default;
}

$unreadCount = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

// Load form fields
$formFields = $pdo->query("SELECT * FROM contact_form_fields ORDER BY sort_order")->fetchAll();

// Section definitions for content editing (hero, info, success — NOT form fields)
$sections = [
    'contact_hero' => [
        'label' => 'Hero Section',
        'fields' => [
            'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
            'headline' => ['label' => 'Headline (HTML allowed)', 'type' => 'textarea'],
            'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
        ],
    ],
    'contact_info' => [
        'label' => 'Contact Info Section',
        'fields' => [
            'heading' => ['label' => 'Section Heading', 'type' => 'text'],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'email_label' => ['label' => 'Email Label', 'type' => 'text'],
            'email' => ['label' => 'Email Address', 'type' => 'text'],
            'phone_label' => ['label' => 'Phone Label', 'type' => 'text'],
            'phone' => ['label' => 'Phone Display', 'type' => 'text'],
            'phone_href' => ['label' => 'Phone Link (e.g. +18005551234)', 'type' => 'text'],
            'response_label' => ['label' => 'Response Time Label', 'type' => 'text'],
            'response_text' => ['label' => 'Response Time Text', 'type' => 'text'],
        ],
    ],
    'contact_success' => [
        'label' => 'Success Message',
        'fields' => [
            'heading' => ['label' => 'Success Heading', 'type' => 'text'],
            'text' => ['label' => 'Success Text', 'type' => 'textarea'],
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Page | Agile & Co Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .field-manager-list { display: flex; flex-direction: column; gap: 8px; }
        .field-manager-item { background: var(--gray-900); border: 1px solid var(--gray-800); border-radius: 8px; overflow: hidden; }
        .field-manager-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; cursor: pointer; gap: 12px; }
        .field-manager-header:hover { background: rgba(255,255,255,0.02); }
        .field-manager-info { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
        .field-manager-info h4 { margin: 0; font-size: 14px; font-weight: 500; }
        .field-manager-badges { display: flex; gap: 6px; align-items: center; }
        .field-badge { font-size: 11px; padding: 2px 8px; border-radius: 4px; background: var(--gray-800); color: var(--gray-400); }
        .field-badge.required { background: rgba(76, 201, 240, 0.15); color: var(--accent); }
        .field-badge.core { background: rgba(255, 200, 50, 0.15); color: #ffc832; }
        .field-badge.inactive { background: rgba(255, 107, 107, 0.15); color: #ff6b6b; }
        .field-manager-actions { display: flex; gap: 8px; align-items: center; }
        .field-manager-body { display: none; padding: 16px; border-top: 1px solid var(--gray-800); }
        .field-manager-item.open .field-manager-body { display: block; }
        .field-edit-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .field-edit-grid .full-width { grid-column: 1 / -1; }
        .toggle-btn { padding: 4px 10px; font-size: 12px; border-radius: 4px; border: 1px solid var(--gray-700); background: transparent; color: var(--gray-400); cursor: pointer; }
        .toggle-btn:hover { border-color: var(--accent); color: var(--accent); }
        .toggle-btn.active { background: rgba(76, 201, 240, 0.15); border-color: var(--accent); color: var(--accent); }
        .delete-field-btn { padding: 4px 10px; font-size: 12px; border-radius: 4px; border: 1px solid rgba(255, 107, 107, 0.3); background: transparent; color: #ff6b6b; cursor: pointer; }
        .delete-field-btn:hover { background: rgba(255, 107, 107, 0.1); }
        .add-field-form { background: var(--gray-900); border: 1px solid var(--gray-800); border-radius: 8px; padding: 20px; margin-top: 12px; display: none; }
        .add-field-form.show { display: block; }
        .add-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .add-field-grid .full-width { grid-column: 1 / -1; }
        .opt-row { display: flex; gap: 8px; margin-bottom: 8px; align-items: center; }
        .opt-row input { flex: 1; }
        .opt-remove { background: none; border: none; color: #ff6b6b; cursor: pointer; font-size: 18px; padding: 0 4px; }
        .add-opt-btn { font-size: 13px; color: var(--accent); background: none; border: none; cursor: pointer; padding: 4px 0; }
        .add-opt-btn:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo"><a href="dashboard.php">Agile & Co</a></div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
                <div class="admin-nav-divider"></div>
                <div class="admin-nav-label">Content</div>
                <a href="homepage.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Homepage
                </a>
                <a href="services.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Services
                </a>
                <a href="industries.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Industries
                </a>
                <a href="core.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Core
                </a>
                <a href="about.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    About
                </a>
                <a href="contact-page.php" class="admin-nav-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    Contact Page
                </a>
                <div class="admin-nav-divider"></div>
                <a href="contacts.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Contacts
                    <?php if ($unreadCount > 0): ?><span class="admin-badge"><?= $unreadCount ?></span><?php endif; ?>
                </a>
                <a href="posts.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    Blog Posts
                </a>
                <a href="history.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    History
                </a>
                <a href="users.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Users
                </a>
                <a href="email-settings.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Email Settings
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="../index.php" class="admin-nav-item">&larr; View Site</a>
                <a href="logout.php" class="admin-nav-item" style="color: #ff6b6b;">Log Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Edit Contact Page</h1>
                    <p>Manage the content displayed on the Contact page</p>
                </div>
            </div>

            <?php if ($success): ?>
                <div style="background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(76, 201, 240, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: var(--accent); font-size: 14px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Page Content Sections (Hero, Info, Success) -->
            <form method="POST">
                <input type="hidden" name="action" value="save_content">
                <?php foreach ($sections as $sectionKey => $sectionData): ?>
                    <div class="admin-accordion-item">
                        <div class="admin-accordion-header" onclick="this.parentElement.classList.toggle('open')">
                            <h3><?= $sectionData['label'] ?></h3>
                            <span class="accordion-arrow">&#9662;</span>
                        </div>
                        <div class="admin-accordion-body">
                            <?php foreach ($sectionData['fields'] as $fieldKey => $fieldInfo): ?>
                                <div class="form-group">
                                    <label><?= $fieldInfo['label'] ?></label>
                                    <?php $value = htmlspecialchars(sc($content, $sectionKey, $fieldKey, $defaults[$sectionKey][$fieldKey] ?? '')); ?>
                                    <?php if ($fieldInfo['type'] === 'textarea'): ?>
                                        <textarea name="content[<?= $sectionKey ?>][<?= $fieldKey ?>]" rows="3"><?= $value ?></textarea>
                                    <?php else: ?>
                                        <input type="text" name="content[<?= $sectionKey ?>][<?= $fieldKey ?>]" value="<?= $value ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">Save Page Content</button>
                    <a href="../contact.php" target="_blank" class="btn btn-secondary" style="padding: 12px 24px;">Preview Contact Page</a>
                </div>
            </form>

            <!-- Form Fields Manager -->
            <div style="margin-top: 40px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div>
                        <h2 style="font-size: 22px; margin: 0;">Form Fields</h2>
                        <p style="color: var(--gray-400); font-size: 14px; margin: 4px 0 0;">Manage the fields shown on the contact form</p>
                    </div>
                    <button type="button" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px;" onclick="document.getElementById('addFieldForm').classList.toggle('show')">+ Add Field</button>
                </div>

                <!-- Add Field Form -->
                <div id="addFieldForm" class="add-field-form">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_field">
                        <div class="add-field-grid">
                            <div class="form-group">
                                <label>Field Label</label>
                                <input type="text" name="field_label" placeholder="e.g. Budget" required oninput="this.form.field_key.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')">
                            </div>
                            <div class="form-group">
                                <label>Field Key</label>
                                <input type="text" name="field_key" placeholder="e.g. budget" required pattern="[a-z0-9_]+">
                            </div>
                            <div class="form-group">
                                <label>Field Type</label>
                                <select name="field_type">
                                    <option value="text">Text</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Phone</option>
                                    <option value="textarea">Text Area</option>
                                    <option value="select">Dropdown</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Placeholder</label>
                                <input type="text" name="field_placeholder" placeholder="Placeholder text...">
                            </div>
                            <div class="form-group">
                                <label>Width</label>
                                <select name="field_width">
                                    <option value="full">Full Width</option>
                                    <option value="half">Half Width</option>
                                </select>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; gap: 8px; padding-top: 28px;">
                                <input type="checkbox" name="field_required" id="addFieldRequired" style="width: auto;">
                                <label for="addFieldRequired" style="margin: 0;">Required</label>
                            </div>
                        </div>
                        <div style="margin-top: 16px; display: flex; gap: 8px;">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 20px; font-size: 14px;">Add Field</button>
                            <button type="button" class="btn btn-secondary" style="padding: 8px 20px; font-size: 14px;" onclick="document.getElementById('addFieldForm').classList.remove('show')">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Field List (Editable) -->
                <form method="POST">
                    <input type="hidden" name="action" value="update_fields">
                    <div class="field-manager-list">
                        <?php foreach ($formFields as $f): ?>
                        <div class="field-manager-item" id="field-<?= $f['id'] ?>">
                            <input type="hidden" name="field_ids[]" value="<?= $f['id'] ?>">
                            <div class="field-manager-header" onclick="this.parentElement.classList.toggle('open')">
                                <div class="field-manager-info">
                                    <h4><?= htmlspecialchars($f['field_label']) ?></h4>
                                    <div class="field-manager-badges">
                                        <span class="field-badge"><?= htmlspecialchars($f['field_type']) ?></span>
                                        <?php if ($f['is_core']): ?><span class="field-badge core">core</span><?php endif; ?>
                                        <?php if ($f['is_required']): ?><span class="field-badge required">required</span><?php endif; ?>
                                        <?php if (!$f['is_active']): ?><span class="field-badge inactive">hidden</span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="field-manager-actions" onclick="event.stopPropagation()">
                                    <?php if (!$f['is_core']): ?>
                                        <button type="submit" form="toggle-<?= $f['id'] ?>" class="toggle-btn <?= $f['is_active'] ? 'active' : '' ?>" title="<?= $f['is_active'] ? 'Click to hide' : 'Click to show' ?>">
                                            <?= $f['is_active'] ? 'Visible' : 'Hidden' ?>
                                        </button>
                                        <button type="submit" form="delete-<?= $f['id'] ?>" class="delete-field-btn" onclick="return confirm('Delete this field? This cannot be undone.')">Delete</button>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: var(--gray-500);">Always visible</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="field-manager-body">
                                <div class="field-edit-grid">
                                    <div class="form-group">
                                        <label>Label</label>
                                        <input type="text" name="label_<?= $f['id'] ?>" value="<?= htmlspecialchars($f['field_label']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Placeholder</label>
                                        <input type="text" name="placeholder_<?= $f['id'] ?>" value="<?= htmlspecialchars($f['placeholder']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Width</label>
                                        <select name="width_<?= $f['id'] ?>">
                                            <option value="full" <?= $f['field_width'] === 'full' ? 'selected' : '' ?>>Full Width</option>
                                            <option value="half" <?= $f['field_width'] === 'half' ? 'selected' : '' ?>>Half Width</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Sort Order</label>
                                        <input type="number" name="order_<?= $f['id'] ?>" value="<?= $f['sort_order'] ?>" min="0">
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center; gap: 8px; padding-top: 28px;">
                                        <input type="checkbox" name="required_<?= $f['id'] ?>" id="req-<?= $f['id'] ?>" <?= $f['is_required'] ? 'checked' : '' ?> style="width: auto;" <?= $f['is_core'] ? 'disabled checked' : '' ?>>
                                        <label for="req-<?= $f['id'] ?>" style="margin: 0;">Required</label>
                                        <?php if ($f['is_core']): ?>
                                            <input type="hidden" name="required_<?= $f['id'] ?>" value="1">
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($f['field_type'] === 'select'): ?>
                                    <div class="form-group full-width">
                                        <label>Dropdown Options</label>
                                        <div id="opts-<?= $f['id'] ?>">
                                            <?php
                                            $opts = $f['select_options'] ? json_decode($f['select_options'], true) : [];
                                            foreach ($opts as $opt):
                                            ?>
                                            <div class="opt-row">
                                                <input type="text" name="opt_values_<?= $f['id'] ?>[]" value="<?= htmlspecialchars($opt['value'] ?? '') ?>" placeholder="Value">
                                                <input type="text" name="opt_labels_<?= $f['id'] ?>[]" value="<?= htmlspecialchars($opt['label'] ?? '') ?>" placeholder="Label">
                                                <button type="button" class="opt-remove" onclick="this.parentElement.remove()">&times;</button>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="add-opt-btn" onclick="addOption(<?= $f['id'] ?>)">+ Add Option</button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">Save Form Fields</button>
                    </div>
                </form>

                <!-- Hidden toggle/delete forms -->
                <?php foreach ($formFields as $f): ?>
                    <?php if (!$f['is_core']): ?>
                    <form id="toggle-<?= $f['id'] ?>" method="POST" style="display:none;">
                        <input type="hidden" name="action" value="toggle_field">
                        <input type="hidden" name="field_id" value="<?= $f['id'] ?>">
                    </form>
                    <form id="delete-<?= $f['id'] ?>" method="POST" style="display:none;">
                        <input type="hidden" name="action" value="delete_field">
                        <input type="hidden" name="field_id" value="<?= $f['id'] ?>">
                    </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
    function addOption(fieldId) {
        var container = document.getElementById('opts-' + fieldId);
        var row = document.createElement('div');
        row.className = 'opt-row';
        row.innerHTML = '<input type="text" name="opt_values_' + fieldId + '[]" placeholder="Value">' +
                         '<input type="text" name="opt_labels_' + fieldId + '[]" placeholder="Label">' +
                         '<button type="button" class="opt-remove" onclick="this.parentElement.remove()">&times;</button>';
        container.appendChild(row);
    }
    </script>
</body>
</html>

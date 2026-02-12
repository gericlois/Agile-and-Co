<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$defaults = require '../config/homepage-defaults.php';
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        logActivity($pdo, 'update', 'content', 'Updated homepage content');
        $success = 'Homepage content saved successfully.';
    } catch (PDOException $e) {
        $error = 'Error saving content. Please try again.';
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

// Section definitions for the form
$sections = [
    'hero' => [
        'label' => 'Hero Section',
        'fields' => [
            'headline_line1' => ['label' => 'Headline Line 1 (accent word)', 'type' => 'text'],
            'headline_line2' => ['label' => 'Headline Line 2', 'type' => 'text'],
            'headline_line3' => ['label' => 'Headline Line 3', 'type' => 'text'],
            'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
            'cta_primary_text' => ['label' => 'Primary Button Text', 'type' => 'text'],
            'cta_secondary_text' => ['label' => 'Secondary Button Text', 'type' => 'text'],
        ],
    ],
    'social_proof' => [
        'label' => 'Stats / Social Proof',
        'fields' => [
            'stat1_number' => ['label' => 'Stat 1 Number', 'type' => 'text'],
            'stat1_label' => ['label' => 'Stat 1 Label', 'type' => 'text'],
            'stat2_number' => ['label' => 'Stat 2 Number', 'type' => 'text'],
            'stat2_label' => ['label' => 'Stat 2 Label', 'type' => 'text'],
            'stat3_number' => ['label' => 'Stat 3 Number', 'type' => 'text'],
            'stat3_label' => ['label' => 'Stat 3 Label', 'type' => 'text'],
        ],
    ],
    'problem' => [
        'label' => 'Problem Section',
        'fields' => [
            'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
            'headline' => ['label' => 'Headline', 'type' => 'text'],
            'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
            'paragraph2' => ['label' => 'Paragraph 2 (HTML allowed)', 'type' => 'textarea'],
            'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
            'paragraph4' => ['label' => 'Paragraph 4', 'type' => 'textarea'],
            'highlight' => ['label' => 'Highlight Text', 'type' => 'text'],
        ],
    ],
    'core' => [
        'label' => 'Core Section',
        'fields' => [
            'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
            'headline' => ['label' => 'Headline', 'type' => 'text'],
            'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
            'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
            'card1_title' => ['label' => 'Card 1 Title', 'type' => 'text'],
            'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
            'card1_translation' => ['label' => 'Card 1 Translation', 'type' => 'textarea'],
            'card2_title' => ['label' => 'Card 2 Title', 'type' => 'text'],
            'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
            'card2_translation' => ['label' => 'Card 2 Translation', 'type' => 'textarea'],
            'card3_title' => ['label' => 'Card 3 Title', 'type' => 'text'],
            'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
            'card3_translation' => ['label' => 'Card 3 Translation', 'type' => 'textarea'],
            'cta_text' => ['label' => 'CTA Button Text', 'type' => 'text'],
        ],
    ],
    'services' => [
        'label' => 'Services Section',
        'fields' => [
            'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
            'headline' => ['label' => 'Headline', 'type' => 'text'],
            'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
            'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
            'card1_title' => ['label' => 'Card 1 Title (SEO)', 'type' => 'text'],
            'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
            'card2_title' => ['label' => 'Card 2 Title (Google Ads)', 'type' => 'text'],
            'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
            'card3_title' => ['label' => 'Card 3 Title (Meta Ads)', 'type' => 'text'],
            'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
            'card4_title' => ['label' => 'Card 4 Title (Website Design)', 'type' => 'text'],
            'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
        ],
    ],
    'differentiators' => [
        'label' => 'Differentiators Section',
        'fields' => [
            'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
            'headline' => ['label' => 'Headline', 'type' => 'text'],
            'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
            'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
            'item1_title' => ['label' => 'Item 1 Title', 'type' => 'text'],
            'item1_text' => ['label' => 'Item 1 Text', 'type' => 'textarea'],
            'item2_title' => ['label' => 'Item 2 Title', 'type' => 'text'],
            'item2_text' => ['label' => 'Item 2 Text', 'type' => 'textarea'],
            'item3_title' => ['label' => 'Item 3 Title', 'type' => 'text'],
            'item3_text' => ['label' => 'Item 3 Text', 'type' => 'textarea'],
            'item4_title' => ['label' => 'Item 4 Title', 'type' => 'text'],
            'item4_text' => ['label' => 'Item 4 Text', 'type' => 'textarea'],
        ],
    ],
    'testimonials' => [
        'label' => 'Testimonials Section',
        'fields' => [
            'headline' => ['label' => 'Section Headline', 'type' => 'text'],
            'quote1_text' => ['label' => 'Quote 1 Text', 'type' => 'textarea'],
            'quote1_author' => ['label' => 'Quote 1 Author', 'type' => 'text'],
            'quote2_text' => ['label' => 'Quote 2 Text', 'type' => 'textarea'],
            'quote2_author' => ['label' => 'Quote 2 Author', 'type' => 'text'],
            'quote3_text' => ['label' => 'Quote 3 Text', 'type' => 'textarea'],
            'quote3_author' => ['label' => 'Quote 3 Author', 'type' => 'text'],
            'quote4_text' => ['label' => 'Quote 4 Text', 'type' => 'textarea'],
            'quote4_author' => ['label' => 'Quote 4 Author', 'type' => 'text'],
        ],
    ],
    'process' => [
        'label' => 'Process Section',
        'fields' => [
            'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
            'headline' => ['label' => 'Headline', 'type' => 'text'],
            'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
            'step1_title' => ['label' => 'Step 1 Title', 'type' => 'text'],
            'step1_text' => ['label' => 'Step 1 Text', 'type' => 'textarea'],
            'step2_title' => ['label' => 'Step 2 Title', 'type' => 'text'],
            'step2_text' => ['label' => 'Step 2 Text', 'type' => 'textarea'],
            'step3_title' => ['label' => 'Step 3 Title', 'type' => 'text'],
            'step3_text' => ['label' => 'Step 3 Text', 'type' => 'textarea'],
            'cta_text' => ['label' => 'CTA Button Text', 'type' => 'text'],
        ],
    ],
    'final_cta' => [
        'label' => 'Final CTA Section',
        'fields' => [
            'headline' => ['label' => 'Headline', 'type' => 'text'],
            'subtitle' => ['label' => 'Subtitle', 'type' => 'text'],
            'cta_text' => ['label' => 'Button Text', 'type' => 'text'],
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Homepage | Agile & Co Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="admin.css">
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
                <a href="homepage.php" class="admin-nav-item active">
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
                <a href="contact-page.php" class="admin-nav-item">
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
                <a href="../index.php" class="admin-nav-item">← View Site</a>
                <a href="logout.php" class="admin-nav-item" style="color: #ff6b6b;">Log Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Edit Homepage</h1>
                    <p>Manage the content displayed on the homepage</p>
                </div>
            </div>

            <?php if ($success): ?>
                <div style="background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(76, 201, 240, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: var(--accent); font-size: 14px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
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
                                    <?php $value = htmlspecialchars(sc($content, $sectionKey, $fieldKey, $defaults[$sectionKey][$fieldKey])); ?>
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
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">Save All Changes</button>
                    <a href="../index.php" target="_blank" class="btn btn-secondary" style="padding: 12px 24px;">Preview Site</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>

<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$allDefaults = require '../config/services-defaults.php';
$success = '';
$error = '';

// Load services from database
$serviceRows = $pdo->query("SELECT * FROM services ORDER BY sort_order, id")->fetchAll();
$validPages = [];
$pageLabels = [];
$previewLinks = [];
$customSlugs = [];
$activeStatus = [];

$customPreviewLinks = [
    'seo' => '../services-seo.php',
    'gads' => '../services-google-ads.php',
    'meta' => '../services-meta-ads.php',
    'webdesign' => '../services-web-design.php',
];

foreach ($serviceRows as $svc) {
    $validPages[] = $svc['slug'];
    $pageLabels[$svc['slug']] = $svc['label'];
    $activeStatus[$svc['slug']] = (int)$svc['is_active'];
    if ($svc['is_custom']) {
        $previewLinks[$svc['slug']] = $customPreviewLinks[$svc['slug']] ?? '../service.php?s=' . $svc['slug'];
        $customSlugs[] = $svc['slug'];
    } else {
        $previewLinks[$svc['slug']] = '../service.php?s=' . $svc['slug'];
    }
}

// Handle add service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_service') {
    $newLabel = trim($_POST['service_name'] ?? '');
    $newSlug = trim($_POST['service_slug'] ?? '');
    $newSlug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $newSlug)));

    if (empty($newLabel) || empty($newSlug)) {
        $error = 'Service name and slug are required.';
    } elseif (in_array($newSlug, $validPages)) {
        $error = 'A service with this slug already exists.';
    } else {
        try {
            $maxOrder = $pdo->query("SELECT MAX(sort_order) FROM services")->fetchColumn();
            $stmt = $pdo->prepare("INSERT INTO services (slug, label, is_custom, sort_order) VALUES (?, ?, 0, ?)");
            $stmt->execute([$newSlug, $newLabel, ($maxOrder ?: 0) + 1]);

            // Insert default content
            $insertStmt = $pdo->prepare(
                "INSERT INTO site_content (section, field_key, field_value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)"
            );
            $insertStmt->execute([$newSlug . '_hero', 'pre_headline', $newLabel . ' Services']);
            $insertStmt->execute([$newSlug . '_hero', 'headline', $newLabel]);
            $insertStmt->execute([$newSlug . '_hero', 'headline_accent', 'Powered by AI.']);
            $insertStmt->execute([$newSlug . '_hero', 'subtitle', 'AI-powered ' . strtolower($newLabel) . ' services for local service businesses.']);
            $insertStmt->execute([$newSlug . '_hero', 'cta_primary', 'Get Started']);
            $insertStmt->execute([$newSlug . '_hero', 'cta_secondary', 'Learn About Core']);

            logActivity($pdo, 'create', 'service', 'Added service: ' . $newLabel);
            $success = 'Service "' . htmlspecialchars($newLabel) . '" added successfully.';

            // Reload services
            $serviceRows = $pdo->query("SELECT * FROM services ORDER BY sort_order, id")->fetchAll();
            $validPages = [];
            $pageLabels = [];
            $previewLinks = [];
            $customSlugs = [];
            $activeStatus = [];
            foreach ($serviceRows as $svc) {
                $validPages[] = $svc['slug'];
                $pageLabels[$svc['slug']] = $svc['label'];
                $activeStatus[$svc['slug']] = (int)$svc['is_active'];
                if ($svc['is_custom']) {
                    $previewLinks[$svc['slug']] = $customPreviewLinks[$svc['slug']] ?? '../service.php?s=' . $svc['slug'];
                    $customSlugs[] = $svc['slug'];
                } else {
                    $previewLinks[$svc['slug']] = '../service.php?s=' . $svc['slug'];
                }
            }
        } catch (PDOException $e) {
            $error = 'Error adding service. Please try again.';
        }
    }
}

// Handle delete service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_service') {
    $deleteSlug = trim($_POST['slug'] ?? '');
    if (!empty($deleteSlug) && !in_array($deleteSlug, $customSlugs)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM services WHERE slug = ? AND is_custom = 0");
            $stmt->execute([$deleteSlug]);

            // Delete all site_content rows for this service
            $stmt = $pdo->prepare("DELETE FROM site_content WHERE section LIKE ?");
            $stmt->execute([$deleteSlug . '_%']);

            logActivity($pdo, 'delete', 'service', 'Deleted service: ' . $deleteSlug);
            $success = 'Service deleted successfully.';

            // Redirect to base services page
            header('Location: services.php?deleted=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Error deleting service. Please try again.';
        }
    } else {
        $error = 'Cannot delete built-in services.';
    }
}

// Handle toggle service visibility
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_service') {
    $toggleSlug = trim($_POST['slug'] ?? '');
    if (!empty($toggleSlug)) {
        $pdo->prepare("UPDATE services SET is_active = 1 - is_active WHERE slug = ?")->execute([$toggleSlug]);
        logActivity($pdo, 'toggle', 'service', 'Toggled visibility: ' . $toggleSlug);
        header('Location: services.php?page=' . urlencode($toggleSlug));
        exit;
    }
}

if (isset($_GET['deleted'])) {
    $success = 'Service deleted successfully.';
    // Reload services
    $serviceRows = $pdo->query("SELECT * FROM services ORDER BY sort_order, id")->fetchAll();
    $validPages = [];
    $pageLabels = [];
    $previewLinks = [];
    $customSlugs = [];
    $activeStatus = [];
    foreach ($serviceRows as $svc) {
        $validPages[] = $svc['slug'];
        $pageLabels[$svc['slug']] = $svc['label'];
        $activeStatus[$svc['slug']] = (int)$svc['is_active'];
        if ($svc['is_custom']) {
            $previewLinks[$svc['slug']] = $customPreviewLinks[$svc['slug']] ?? '../service.php?s=' . $svc['slug'];
            $customSlugs[] = $svc['slug'];
        } else {
            $previewLinks[$svc['slug']] = '../service.php?s=' . $svc['slug'];
        }
    }
}

// Current tab
$currentPage = $_GET['page'] ?? ($validPages[0] ?? 'seo');
if (!in_array($currentPage, $validPages)) {
    $currentPage = $validPages[0] ?? 'seo';
}

$isCustom = in_array($currentPage, $customSlugs);

// Handle content form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_content') {
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
        logActivity($pdo, 'update', 'content', 'Updated service content: ' . $pageLabels[$currentPage]);
        $success = $pageLabels[$currentPage] . ' content saved successfully.';
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

// Build standard section definitions for new (non-custom) services
function buildServiceSections($slug) {
    return [
        $slug . '_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        $slug . '_intro' => [
            'label' => 'Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight text)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'paragraph4' => ['label' => 'Paragraph 4 (HTML allowed)', 'type' => 'textarea'],
            ],
        ],
        $slug . '_services' => [
            'label' => 'Services Grid',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Card 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Card 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Card 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Card 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
                'card5_title' => ['label' => 'Card 5 Title', 'type' => 'text'],
                'card5_text' => ['label' => 'Card 5 Text', 'type' => 'textarea'],
                'card6_title' => ['label' => 'Card 6 Title', 'type' => 'text'],
                'card6_text' => ['label' => 'Card 6 Text', 'type' => 'textarea'],
            ],
        ],
        $slug . '_faq' => [
            'label' => 'FAQ Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'q1' => ['label' => 'Question 1', 'type' => 'text'],
                'a1' => ['label' => 'Answer 1', 'type' => 'textarea'],
                'q2' => ['label' => 'Question 2', 'type' => 'text'],
                'a2' => ['label' => 'Answer 2', 'type' => 'textarea'],
                'q3' => ['label' => 'Question 3', 'type' => 'text'],
                'a3' => ['label' => 'Answer 3', 'type' => 'textarea'],
                'q4' => ['label' => 'Question 4', 'type' => 'text'],
                'a4' => ['label' => 'Answer 4', 'type' => 'textarea'],
            ],
        ],
        $slug . '_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
    ];
}

// Hardcoded section definitions for custom services
$customPageSections = [
    'seo' => [
        'seo_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        'seo_core_intro' => [
            'label' => 'Core Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight text)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'paragraph4' => ['label' => 'Paragraph 4 (HTML allowed)', 'type' => 'textarea'],
            ],
        ],
        'seo_acronyms' => [
            'label' => 'Acronyms Section (AEO/GEO/AIO)',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'intro' => ['label' => 'Intro Text', 'type' => 'textarea'],
                'card1_abbr' => ['label' => 'Card 1 Abbreviation', 'type' => 'text'],
                'card1_name' => ['label' => 'Card 1 Full Name', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Description', 'type' => 'textarea'],
                'card2_abbr' => ['label' => 'Card 2 Abbreviation', 'type' => 'text'],
                'card2_name' => ['label' => 'Card 2 Full Name', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Description', 'type' => 'textarea'],
                'card3_abbr' => ['label' => 'Card 3 Abbreviation', 'type' => 'text'],
                'card3_name' => ['label' => 'Card 3 Full Name', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Description', 'type' => 'textarea'],
                'truth_title' => ['label' => 'Truth Section Title', 'type' => 'text'],
                'truth_text' => ['label' => 'Truth Section Text (HTML allowed)', 'type' => 'textarea'],
            ],
        ],
        'seo_process' => [
            'label' => 'Process Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'step1_title' => ['label' => 'Step 1 Title', 'type' => 'text'],
                'step1_text' => ['label' => 'Step 1 Text', 'type' => 'textarea'],
                'step2_title' => ['label' => 'Step 2 Title', 'type' => 'text'],
                'step2_text' => ['label' => 'Step 2 Text', 'type' => 'textarea'],
                'step3_title' => ['label' => 'Step 3 Title', 'type' => 'text'],
                'step3_text' => ['label' => 'Step 3 Text', 'type' => 'textarea'],
                'step4_title' => ['label' => 'Step 4 Title', 'type' => 'text'],
                'step4_text' => ['label' => 'Step 4 Text', 'type' => 'textarea'],
            ],
        ],
        'seo_services' => [
            'label' => 'Services Grid',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Card 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Card 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Card 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Card 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
                'card5_title' => ['label' => 'Card 5 Title', 'type' => 'text'],
                'card5_text' => ['label' => 'Card 5 Text', 'type' => 'textarea'],
                'card6_title' => ['label' => 'Card 6 Title', 'type' => 'text'],
                'card6_text' => ['label' => 'Card 6 Text', 'type' => 'textarea'],
            ],
        ],
        'seo_faq' => [
            'label' => 'FAQ Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'q1' => ['label' => 'Question 1', 'type' => 'text'],
                'a1' => ['label' => 'Answer 1', 'type' => 'textarea'],
                'q2' => ['label' => 'Question 2', 'type' => 'text'],
                'a2' => ['label' => 'Answer 2', 'type' => 'textarea'],
                'q3' => ['label' => 'Question 3', 'type' => 'text'],
                'a3' => ['label' => 'Answer 3', 'type' => 'textarea'],
                'q4' => ['label' => 'Question 4', 'type' => 'text'],
                'a4' => ['label' => 'Answer 4', 'type' => 'textarea'],
            ],
        ],
        'seo_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
    ],
    'gads' => [
        'gads_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        'gads_core_intro' => [
            'label' => 'Core Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight text)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'paragraph4' => ['label' => 'Paragraph 4 (HTML allowed)', 'type' => 'textarea'],
            ],
        ],
        'gads_comparison' => [
            'label' => 'Comparison Section',
            'fields' => [
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'bad_label' => ['label' => 'Bad Column Label', 'type' => 'text'],
                'bad1' => ['label' => 'Bad Point 1', 'type' => 'text'],
                'bad2' => ['label' => 'Bad Point 2', 'type' => 'text'],
                'bad3' => ['label' => 'Bad Point 3', 'type' => 'text'],
                'bad4' => ['label' => 'Bad Point 4', 'type' => 'text'],
                'bad5' => ['label' => 'Bad Point 5', 'type' => 'text'],
                'good_label' => ['label' => 'Good Column Label', 'type' => 'text'],
                'good1' => ['label' => 'Good Point 1', 'type' => 'text'],
                'good2' => ['label' => 'Good Point 2', 'type' => 'text'],
                'good3' => ['label' => 'Good Point 3', 'type' => 'text'],
                'good4' => ['label' => 'Good Point 4', 'type' => 'text'],
                'good5' => ['label' => 'Good Point 5', 'type' => 'text'],
                'bottom_text' => ['label' => 'Bottom Text (HTML allowed)', 'type' => 'textarea'],
            ],
        ],
        'gads_process' => [
            'label' => 'Process Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'step1_title' => ['label' => 'Step 1 Title', 'type' => 'text'],
                'step1_text' => ['label' => 'Step 1 Text', 'type' => 'textarea'],
                'step2_title' => ['label' => 'Step 2 Title', 'type' => 'text'],
                'step2_text' => ['label' => 'Step 2 Text', 'type' => 'textarea'],
                'step3_title' => ['label' => 'Step 3 Title', 'type' => 'text'],
                'step3_text' => ['label' => 'Step 3 Text', 'type' => 'textarea'],
                'step4_title' => ['label' => 'Step 4 Title', 'type' => 'text'],
                'step4_text' => ['label' => 'Step 4 Text', 'type' => 'textarea'],
            ],
        ],
        'gads_services' => [
            'label' => 'Services Grid',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Card 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Card 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Card 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Card 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
                'card5_title' => ['label' => 'Card 5 Title', 'type' => 'text'],
                'card5_text' => ['label' => 'Card 5 Text', 'type' => 'textarea'],
                'card6_title' => ['label' => 'Card 6 Title', 'type' => 'text'],
                'card6_text' => ['label' => 'Card 6 Text', 'type' => 'textarea'],
            ],
        ],
        'gads_faq' => [
            'label' => 'FAQ Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'q1' => ['label' => 'Question 1', 'type' => 'text'],
                'a1' => ['label' => 'Answer 1', 'type' => 'textarea'],
                'q2' => ['label' => 'Question 2', 'type' => 'text'],
                'a2' => ['label' => 'Answer 2', 'type' => 'textarea'],
                'q3' => ['label' => 'Question 3', 'type' => 'text'],
                'a3' => ['label' => 'Answer 3', 'type' => 'textarea'],
                'q4' => ['label' => 'Question 4', 'type' => 'text'],
                'a4' => ['label' => 'Answer 4', 'type' => 'textarea'],
                'q5' => ['label' => 'Question 5', 'type' => 'text'],
                'a5' => ['label' => 'Answer 5', 'type' => 'textarea'],
            ],
        ],
        'gads_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
    ],
    'meta' => [
        'meta_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        'meta_core_intro' => [
            'label' => 'Core Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight text)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'paragraph4' => ['label' => 'Paragraph 4 (HTML allowed)', 'type' => 'textarea'],
            ],
        ],
        'meta_services' => [
            'label' => 'Services Grid',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Card 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Card 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Card 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Card 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
                'card5_title' => ['label' => 'Card 5 Title', 'type' => 'text'],
                'card5_text' => ['label' => 'Card 5 Text', 'type' => 'textarea'],
                'card6_title' => ['label' => 'Card 6 Title', 'type' => 'text'],
                'card6_text' => ['label' => 'Card 6 Text', 'type' => 'textarea'],
            ],
        ],
        'meta_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
    ],
    'webdesign' => [
        'webdesign_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        'webdesign_core_intro' => [
            'label' => 'Core Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight text)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'paragraph4' => ['label' => 'Paragraph 4', 'type' => 'textarea'],
            ],
        ],
        'webdesign_services' => [
            'label' => 'Services Grid',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Card 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Card 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Card 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Card 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
                'card5_title' => ['label' => 'Card 5 Title', 'type' => 'text'],
                'card5_text' => ['label' => 'Card 5 Text', 'type' => 'textarea'],
                'card6_title' => ['label' => 'Card 6 Title', 'type' => 'text'],
                'card6_text' => ['label' => 'Card 6 Text', 'type' => 'textarea'],
            ],
        ],
        'webdesign_timeline' => [
            'label' => 'Timeline Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'step1_period' => ['label' => 'Phase 1 Period', 'type' => 'text'],
                'step1_title' => ['label' => 'Phase 1 Title', 'type' => 'text'],
                'step1_text' => ['label' => 'Phase 1 Text', 'type' => 'textarea'],
                'step2_period' => ['label' => 'Phase 2 Period', 'type' => 'text'],
                'step2_title' => ['label' => 'Phase 2 Title', 'type' => 'text'],
                'step2_text' => ['label' => 'Phase 2 Text', 'type' => 'textarea'],
                'step3_period' => ['label' => 'Phase 3 Period', 'type' => 'text'],
                'step3_title' => ['label' => 'Phase 3 Title', 'type' => 'text'],
                'step3_text' => ['label' => 'Phase 3 Text', 'type' => 'textarea'],
                'step4_period' => ['label' => 'Phase 4 Period', 'type' => 'text'],
                'step4_title' => ['label' => 'Phase 4 Title', 'type' => 'text'],
                'step4_text' => ['label' => 'Phase 4 Text', 'type' => 'textarea'],
                'step5_period' => ['label' => 'Phase 5 Period', 'type' => 'text'],
                'step5_title' => ['label' => 'Phase 5 Title', 'type' => 'text'],
                'step5_text' => ['label' => 'Phase 5 Text', 'type' => 'textarea'],
            ],
        ],
        'webdesign_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
    ],
];

// Get sections for current page
if ($isCustom && isset($customPageSections[$currentPage])) {
    $sections = $customPageSections[$currentPage];
    $defaults = $allDefaults[$currentPage] ?? [];
} else {
    $sections = buildServiceSections($currentPage);
    $defaults = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Services | Agile & Co Admin</title>
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
                <a href="homepage.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Homepage
                </a>
                <a href="services.php" class="admin-nav-item active">
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
                <a href="../index.php" class="admin-nav-item">&larr; View Site</a>
                <a href="logout.php" class="admin-nav-item" style="color: #ff6b6b;">Log Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Edit Services</h1>
                    <p>Manage content for service pages</p>
                </div>
                <button onclick="document.getElementById('addServiceForm').style.display = document.getElementById('addServiceForm').style.display === 'none' ? 'block' : 'none'" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;">+ Add Service</button>
            </div>

            <!-- Add Service Form -->
            <div id="addServiceForm" style="display: none; background: var(--gray-800); border: 1px solid var(--gray-700); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
                <h3 style="margin-bottom: 16px; font-size: 18px;">Add New Service</h3>
                <form method="POST" action="services.php">
                    <input type="hidden" name="action" value="add_service">
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Service Name</label>
                            <input type="text" name="service_name" placeholder="e.g. Email Marketing" required oninput="this.form.service_slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>URL Slug</label>
                            <input type="text" name="service_slug" placeholder="e.g. email-marketing" required pattern="[a-z0-9\-]+">
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 12px 24px; white-space: nowrap;">Create Service</button>
                    </div>
                    <p style="color: var(--gray-400); font-size: 13px; margin-top: 12px;">The service page will be accessible at: service.php?s=<span id="slugPreview" style="color: var(--accent);">slug</span></p>
                </form>
            </div>

            <?php if ($success): ?>
                <div style="background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(76, 201, 240, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: var(--accent); font-size: 14px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="admin-tabs">
                <?php foreach ($pageLabels as $key => $label): ?>
                    <a href="services.php?page=<?= $key ?>" class="admin-tab <?= $currentPage === $key ? 'active' : '' ?>"><?= htmlspecialchars($label) ?></a>
                <?php endforeach; ?>
            </div>

            <?php if (!$isCustom): ?>
            <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
                <form method="POST" action="services.php" onsubmit="return confirm('Are you sure you want to delete the &quot;<?= htmlspecialchars($pageLabels[$currentPage]) ?>&quot; service? This will remove all its content and cannot be undone.')">
                    <input type="hidden" name="action" value="delete_service">
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($currentPage) ?>">
                    <button type="submit" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px; color: #ff6b6b; border-color: rgba(255, 107, 107, 0.3);">Delete This Service</button>
                </form>
            </div>
            <?php endif; ?>

            <form method="POST" action="services.php?page=<?= $currentPage ?>">
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
                                    <?php
                                    $defaultValue = $defaults[$sectionKey][$fieldKey] ?? '';
                                    $value = htmlspecialchars(sc($content, $sectionKey, $fieldKey, $defaultValue));
                                    ?>
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
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">Save <?= htmlspecialchars($pageLabels[$currentPage]) ?> Changes</button>
                    <a href="<?= $previewLinks[$currentPage] ?>" target="_blank" class="btn btn-secondary" style="padding: 12px 24px;">Preview Page</a>
                </div>
            </form>
        </main>
    </div>

    <script>
    // Update slug preview in add service form
    const slugInput = document.querySelector('input[name="service_slug"]');
    const slugPreview = document.getElementById('slugPreview');
    if (slugInput && slugPreview) {
        slugInput.addEventListener('input', function() {
            slugPreview.textContent = this.value || 'slug';
        });
    }
    </script>
</body>
</html>

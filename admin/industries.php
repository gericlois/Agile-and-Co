<?php
session_start();
require_once '../config/database.php';
require_once '../config/activity-log.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$allDefaults = require '../config/industries-defaults.php';
$success = '';
$error = '';

// Load industries from database
$industryRows = $pdo->query("SELECT * FROM industries ORDER BY sort_order, id")->fetchAll();
$validPages = [];
$pageLabels = [];
$previewLinks = [];
$customSlugs = [];

$customPreviewLinks = [
    'listing' => '../industries.php',
    'hvac' => '../industry-hvac.php',
    'plumbing' => '../industry-plumbing.php',
    'electrical' => '../industry-electrical.php',
    'remodeling' => '../industry-remodeling.php',
    'moving' => '../industry-moving.php',
    'deck' => '../industry-deck.php',
    'landscaping' => '../industry-landscaping.php',
    'gc' => '../industry-general-contractors.php',
    'automotive' => '../industry-automotive.php',
    'medspa' => '../industry-medspa.php',
    'fencing' => '../industry-fencing.php',
];

foreach ($industryRows as $ind) {
    $validPages[] = $ind['slug'];
    $pageLabels[$ind['slug']] = $ind['label'];
    if ($ind['is_custom']) {
        $previewLinks[$ind['slug']] = $customPreviewLinks[$ind['slug']] ?? '../industry.php?i=' . $ind['slug'];
        $customSlugs[] = $ind['slug'];
    } else {
        $previewLinks[$ind['slug']] = '../industry.php?i=' . $ind['slug'];
    }
}

// Handle add industry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_industry') {
    $newLabel = trim($_POST['industry_name'] ?? '');
    $newSlug = trim($_POST['industry_slug'] ?? '');
    $newSlug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $newSlug)));

    if (empty($newLabel) || empty($newSlug)) {
        $error = 'Industry name and slug are required.';
    } elseif (in_array($newSlug, $validPages)) {
        $error = 'An industry with this slug already exists.';
    } else {
        try {
            $maxOrder = $pdo->query("SELECT MAX(sort_order) FROM industries")->fetchColumn();
            $stmt = $pdo->prepare("INSERT INTO industries (slug, label, is_custom, sort_order) VALUES (?, ?, 0, ?)");
            $stmt->execute([$newSlug, $newLabel, ($maxOrder ?: 0) + 1]);

            // Insert default content
            $insertStmt = $pdo->prepare(
                "INSERT INTO site_content (section, field_key, field_value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)"
            );
            $insertStmt->execute([$newSlug . '_hero', 'badge', $newLabel . ' Marketing']);
            $insertStmt->execute([$newSlug . '_hero', 'headline', 'AI-Powered Marketing for']);
            $insertStmt->execute([$newSlug . '_hero', 'headline_accent', $newLabel . ' Companies.']);
            $insertStmt->execute([$newSlug . '_hero', 'subtitle', 'More leads, more booked jobs, and a marketing strategy built specifically for ' . strtolower($newLabel) . ' businesses.']);
            $insertStmt->execute([$newSlug . '_hero', 'cta_primary', 'Get Your Free Audit']);
            $insertStmt->execute([$newSlug . '_hero', 'cta_secondary', 'See How Core Works']);

            logActivity($pdo, 'create', 'industry', 'Added industry: ' . $newLabel);
            $success = 'Industry "' . htmlspecialchars($newLabel) . '" added successfully.';

            // Reload industries
            $industryRows = $pdo->query("SELECT * FROM industries ORDER BY sort_order, id")->fetchAll();
            $validPages = [];
            $pageLabels = [];
            $previewLinks = [];
            $customSlugs = [];
            foreach ($industryRows as $ind) {
                $validPages[] = $ind['slug'];
                $pageLabels[$ind['slug']] = $ind['label'];
                if ($ind['is_custom']) {
                    $previewLinks[$ind['slug']] = $customPreviewLinks[$ind['slug']] ?? '../industry.php?i=' . $ind['slug'];
                    $customSlugs[] = $ind['slug'];
                } else {
                    $previewLinks[$ind['slug']] = '../industry.php?i=' . $ind['slug'];
                }
            }
        } catch (PDOException $e) {
            $error = 'Error adding industry. Please try again.';
        }
    }
}

// Handle delete industry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_industry') {
    $deleteSlug = trim($_POST['slug'] ?? '');
    if (!empty($deleteSlug) && !in_array($deleteSlug, $customSlugs)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM industries WHERE slug = ? AND is_custom = 0");
            $stmt->execute([$deleteSlug]);

            // Delete all site_content rows for this industry
            $stmt = $pdo->prepare("DELETE FROM site_content WHERE section LIKE ?");
            $stmt->execute([$deleteSlug . '_%']);

            logActivity($pdo, 'delete', 'industry', 'Deleted industry: ' . $deleteSlug);
            $success = 'Industry deleted successfully.';

            // Redirect to base industries page
            header('Location: industries.php?deleted=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Error deleting industry. Please try again.';
        }
    } else {
        $error = 'Cannot delete built-in industries.';
    }
}

if (isset($_GET['deleted'])) {
    $success = 'Industry deleted successfully.';
    // Reload industries
    $industryRows = $pdo->query("SELECT * FROM industries ORDER BY sort_order, id")->fetchAll();
    $validPages = [];
    $pageLabels = [];
    $previewLinks = [];
    $customSlugs = [];
    foreach ($industryRows as $ind) {
        $validPages[] = $ind['slug'];
        $pageLabels[$ind['slug']] = $ind['label'];
        if ($ind['is_custom']) {
            $previewLinks[$ind['slug']] = $customPreviewLinks[$ind['slug']] ?? '../industry.php?i=' . $ind['slug'];
            $customSlugs[] = $ind['slug'];
        } else {
            $previewLinks[$ind['slug']] = '../industry.php?i=' . $ind['slug'];
        }
    }
}

// Current tab
$currentPage = $_GET['page'] ?? ($validPages[0] ?? 'listing');
if (!in_array($currentPage, $validPages)) {
    $currentPage = $validPages[0] ?? 'listing';
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
        logActivity($pdo, 'update', 'content', 'Updated industry content: ' . $pageLabels[$currentPage]);
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

// Helper to build standard industry sections (for both custom standard and new industries)
function buildStandardSections($prefix) {
    return [
        $prefix . '_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'badge' => ['label' => 'Badge Text', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        $prefix . '_intro' => [
            'label' => 'Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'card_title' => ['label' => 'Info Card Title', 'type' => 'text'],
                'card_text' => ['label' => 'Info Card Text', 'type' => 'textarea'],
            ],
        ],
        $prefix . '_challenges' => [
            'label' => 'Challenges Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Challenge 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Challenge 1 Text', 'type' => 'textarea'],
                'card1_solution' => ['label' => 'Challenge 1 Solution', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Challenge 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Challenge 2 Text', 'type' => 'textarea'],
                'card2_solution' => ['label' => 'Challenge 2 Solution', 'type' => 'textarea'],
            ],
        ],
        $prefix . '_services' => [
            'label' => 'Services Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Service 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Service 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Service 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Service 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Service 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Service 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Service 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Service 4 Text', 'type' => 'textarea'],
            ],
        ],
        $prefix . '_cta' => [
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

// Hardcoded section definitions for custom industry pages
$customPageSections = [
    'listing' => [
        'listing_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
            ],
        ],
        'listing_grid' => [
            'label' => 'Industry Cards Grid',
            'fields' => [
                'card1_title' => ['label' => 'Card 1 Title (HVAC)', 'type' => 'text'],
                'card1_text' => ['label' => 'Card 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Card 2 Title (Plumbing)', 'type' => 'text'],
                'card2_text' => ['label' => 'Card 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Card 3 Title (Electrical)', 'type' => 'text'],
                'card3_text' => ['label' => 'Card 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Card 4 Title (Remodeling)', 'type' => 'text'],
                'card4_text' => ['label' => 'Card 4 Text', 'type' => 'textarea'],
                'card5_title' => ['label' => 'Card 5 Title (Moving)', 'type' => 'text'],
                'card5_text' => ['label' => 'Card 5 Text', 'type' => 'textarea'],
                'card6_title' => ['label' => 'Card 6 Title (Deck)', 'type' => 'text'],
                'card6_text' => ['label' => 'Card 6 Text', 'type' => 'textarea'],
                'card7_title' => ['label' => 'Card 7 Title (Landscaping)', 'type' => 'text'],
                'card7_text' => ['label' => 'Card 7 Text', 'type' => 'textarea'],
                'card8_title' => ['label' => 'Card 8 Title (Gen. Contractors)', 'type' => 'text'],
                'card8_text' => ['label' => 'Card 8 Text', 'type' => 'textarea'],
                'card9_title' => ['label' => 'Card 9 Title (Automotive)', 'type' => 'text'],
                'card9_text' => ['label' => 'Card 9 Text', 'type' => 'textarea'],
                'card10_title' => ['label' => 'Card 10 Title (Med Spa)', 'type' => 'text'],
                'card10_text' => ['label' => 'Card 10 Text', 'type' => 'textarea'],
                'card11_title' => ['label' => 'Card 11 Title (Fencing)', 'type' => 'text'],
                'card11_text' => ['label' => 'Card 11 Text', 'type' => 'textarea'],
            ],
        ],
        'listing_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'CTA Text', 'type' => 'text'],
            ],
        ],
    ],
    'hvac' => [
        'hvac_hero' => [
            'label' => 'Hero Section',
            'fields' => [
                'badge' => ['label' => 'Badge Text', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
        'hvac_intro' => [
            'label' => 'Intro Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'paragraph1' => ['label' => 'Paragraph 1', 'type' => 'textarea'],
                'paragraph2' => ['label' => 'Paragraph 2 (highlight)', 'type' => 'text'],
                'paragraph3' => ['label' => 'Paragraph 3', 'type' => 'textarea'],
                'card_title' => ['label' => 'Info Card Title', 'type' => 'text'],
                'card_text' => ['label' => 'Info Card Text', 'type' => 'textarea'],
            ],
        ],
        'hvac_core' => [
            'label' => 'Core AI Features',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'intro_text' => ['label' => 'Intro Text', 'type' => 'textarea'],
                'feature1_title' => ['label' => 'Feature 1 Title', 'type' => 'text'],
                'feature1_text' => ['label' => 'Feature 1 Text', 'type' => 'textarea'],
                'feature2_title' => ['label' => 'Feature 2 Title', 'type' => 'text'],
                'feature2_text' => ['label' => 'Feature 2 Text', 'type' => 'textarea'],
                'feature3_title' => ['label' => 'Feature 3 Title', 'type' => 'text'],
                'feature3_text' => ['label' => 'Feature 3 Text', 'type' => 'textarea'],
                'feature4_title' => ['label' => 'Feature 4 Title', 'type' => 'text'],
                'feature4_text' => ['label' => 'Feature 4 Text', 'type' => 'textarea'],
            ],
        ],
        'hvac_challenges' => [
            'label' => 'Challenges Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'card1_title' => ['label' => 'Challenge 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Challenge 1 Text', 'type' => 'textarea'],
                'card1_solution' => ['label' => 'Challenge 1 Solution', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Challenge 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Challenge 2 Text', 'type' => 'textarea'],
                'card2_solution' => ['label' => 'Challenge 2 Solution', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Challenge 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Challenge 3 Text', 'type' => 'textarea'],
                'card3_solution' => ['label' => 'Challenge 3 Solution', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Challenge 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Challenge 4 Text', 'type' => 'textarea'],
                'card4_solution' => ['label' => 'Challenge 4 Solution', 'type' => 'textarea'],
            ],
        ],
        'hvac_services' => [
            'label' => 'Services Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'intro' => ['label' => 'Intro Text', 'type' => 'textarea'],
                'card1_title' => ['label' => 'Service 1 Title', 'type' => 'text'],
                'card1_text' => ['label' => 'Service 1 Text', 'type' => 'textarea'],
                'card2_title' => ['label' => 'Service 2 Title', 'type' => 'text'],
                'card2_text' => ['label' => 'Service 2 Text', 'type' => 'textarea'],
                'card3_title' => ['label' => 'Service 3 Title', 'type' => 'text'],
                'card3_text' => ['label' => 'Service 3 Text', 'type' => 'textarea'],
                'card4_title' => ['label' => 'Service 4 Title', 'type' => 'text'],
                'card4_text' => ['label' => 'Service 4 Text', 'type' => 'textarea'],
            ],
        ],
        'hvac_timeline' => [
            'label' => 'Timeline Section',
            'fields' => [
                'pre_headline' => ['label' => 'Pre-headline', 'type' => 'text'],
                'headline' => ['label' => 'Headline (before accent)', 'type' => 'text'],
                'headline_accent' => ['label' => 'Headline Accent Part', 'type' => 'text'],
                'step1_period' => ['label' => 'Step 1 Period', 'type' => 'text'],
                'step1_title' => ['label' => 'Step 1 Title', 'type' => 'text'],
                'step1_text' => ['label' => 'Step 1 Text', 'type' => 'textarea'],
                'step2_period' => ['label' => 'Step 2 Period', 'type' => 'text'],
                'step2_title' => ['label' => 'Step 2 Title', 'type' => 'text'],
                'step2_text' => ['label' => 'Step 2 Text', 'type' => 'textarea'],
                'step3_period' => ['label' => 'Step 3 Period', 'type' => 'text'],
                'step3_title' => ['label' => 'Step 3 Title', 'type' => 'text'],
                'step3_text' => ['label' => 'Step 3 Text', 'type' => 'textarea'],
                'step4_period' => ['label' => 'Step 4 Period', 'type' => 'text'],
                'step4_title' => ['label' => 'Step 4 Title', 'type' => 'text'],
                'step4_text' => ['label' => 'Step 4 Text', 'type' => 'textarea'],
                'step5_period' => ['label' => 'Step 5 Period', 'type' => 'text'],
                'step5_title' => ['label' => 'Step 5 Title', 'type' => 'text'],
                'step5_text' => ['label' => 'Step 5 Text', 'type' => 'textarea'],
                'bottom_text' => ['label' => 'Bottom Note Text', 'type' => 'textarea'],
            ],
        ],
        'hvac_why' => [
            'label' => 'Why Choose Us',
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
            ],
        ],
        'hvac_faq' => [
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
        'hvac_cta' => [
            'label' => 'Final CTA Section',
            'fields' => [
                'headline' => ['label' => 'Headline', 'type' => 'text'],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'textarea'],
                'cta_primary' => ['label' => 'Primary CTA Text', 'type' => 'text'],
                'cta_secondary' => ['label' => 'Secondary CTA Text', 'type' => 'text'],
            ],
        ],
    ],
    'plumbing' => 'standard',
    'electrical' => 'standard',
    'remodeling' => 'standard',
    'moving' => 'standard',
    'deck' => 'standard',
    'landscaping' => 'standard',
    'gc' => 'standard',
    'automotive' => 'standard',
    'medspa' => 'standard',
    'fencing' => 'standard',
];

// Get sections for current page
if ($isCustom && isset($customPageSections[$currentPage])) {
    if ($customPageSections[$currentPage] === 'standard') {
        $sections = buildStandardSections($currentPage);
    } else {
        $sections = $customPageSections[$currentPage];
    }
    $defaults = $allDefaults[$currentPage] ?? [];
} else {
    $sections = buildStandardSections($currentPage);
    $defaults = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Industries | Agile & Co Admin</title>
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
                <a href="services.php" class="admin-nav-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Services
                </a>
                <a href="industries.php" class="admin-nav-item active">
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
                    <h1>Edit Industries</h1>
                    <p>Manage content for industry pages</p>
                </div>
                <button onclick="document.getElementById('addIndustryForm').style.display = document.getElementById('addIndustryForm').style.display === 'none' ? 'block' : 'none'" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;">+ Add Industry</button>
            </div>

            <!-- Add Industry Form -->
            <div id="addIndustryForm" style="display: none; background: var(--gray-800); border: 1px solid var(--gray-700); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
                <h3 style="margin-bottom: 16px; font-size: 18px;">Add New Industry</h3>
                <form method="POST" action="industries.php">
                    <input type="hidden" name="action" value="add_industry">
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Industry Name</label>
                            <input type="text" name="industry_name" placeholder="e.g. Roofing" required oninput="this.form.industry_slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>URL Slug</label>
                            <input type="text" name="industry_slug" placeholder="e.g. roofing" required pattern="[a-z0-9\-]+">
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 12px 24px; white-space: nowrap;">Create Industry</button>
                    </div>
                    <p style="color: var(--gray-400); font-size: 13px; margin-top: 12px;">The industry page will be accessible at: industry.php?i=<span id="slugPreview" style="color: var(--accent);">slug</span></p>
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
                    <a href="industries.php?page=<?= $key ?>" class="admin-tab <?= $currentPage === $key ? 'active' : '' ?>"><?= htmlspecialchars($label) ?></a>
                <?php endforeach; ?>
            </div>

            <?php if (!$isCustom): ?>
            <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
                <form method="POST" action="industries.php" onsubmit="return confirm('Are you sure you want to delete the &quot;<?= htmlspecialchars($pageLabels[$currentPage]) ?>&quot; industry? This will remove all its content and cannot be undone.')">
                    <input type="hidden" name="action" value="delete_industry">
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($currentPage) ?>">
                    <button type="submit" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px; color: #ff6b6b; border-color: rgba(255, 107, 107, 0.3);">Delete This Industry</button>
                </form>
            </div>
            <?php endif; ?>

            <form method="POST" action="industries.php?page=<?= $currentPage ?>">
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
    // Update slug preview in add industry form
    const slugInput = document.querySelector('input[name="industry_slug"]');
    const slugPreview = document.getElementById('slugPreview');
    if (slugInput && slugPreview) {
        slugInput.addEventListener('input', function() {
            slugPreview.textContent = this.value || 'slug';
        });
    }
    </script>
</body>
</html>

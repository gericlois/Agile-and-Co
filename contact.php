<?php
require_once 'config/database.php';
require_once 'config/lead-scoring.php';
$pageDefaults = require 'config/contact-defaults.php';
$rows = $pdo->query("SELECT section, field_key, field_value FROM site_content")->fetchAll();
$pageContent = [];
foreach ($rows as $row) {
    $pageContent[$row['section']][$row['field_key']] = $row['field_value'];
}
function sc($content, $section, $key, $default = '') {
    return $content[$section][$key] ?? $default;
}
function esc($content, $section, $key, $default = '') {
    return htmlspecialchars(sc($content, $section, $key, $default));
}

// Load active form fields
$formFields = $pdo->query("SELECT * FROM contact_form_fields WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

// DB columns that exist in contacts table (not custom)
$dbColumns = ['first_name', 'last_name', 'email', 'phone', 'company', 'industry', 'message'];

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    foreach ($formFields as $field) {
        if ($field['is_required']) {
            $val = trim($_POST[$field['field_key']] ?? '');
            if (empty($val)) {
                $error = 'Please fill in all required fields.';
                break;
            }
            if ($field['field_type'] === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
                break;
            }
        }
    }

    if (!$error) {
        // Separate core DB columns from custom fields
        $coreData = [];
        $customData = [];
        foreach ($formFields as $field) {
            $val = trim($_POST[$field['field_key']] ?? '');
            if (in_array($field['field_key'], $dbColumns)) {
                $coreData[$field['field_key']] = $val;
            } else {
                $customData[$field['field_key']] = $val;
            }
        }

        // Build INSERT with core columns
        $cols = array_keys($coreData);
        $cols[] = 'custom_fields';
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $colNames = implode(', ', $cols);
        $values = array_values($coreData);
        $values[] = !empty($customData) ? json_encode($customData) : null;

        $stmt = $pdo->prepare("INSERT INTO contacts ($colNames) VALUES ($placeholders)");
        $stmt->execute($values);

        // AI Lead Scoring
        $newContactId = $pdo->lastInsertId();
        try { scoreLeadWithAI($pdo, $newContactId); } catch (Exception $e) {}

        // Send email notifications to all active recipients
        try {
            $notifyEmails = $pdo->query("SELECT email FROM notification_emails WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            $notifyEmails = [];
        }
        if (!empty($notifyEmails)) {
            $name = ($coreData['first_name'] ?? '') . ' ' . ($coreData['last_name'] ?? '');
            $emailFrom = $coreData['email'] ?? 'noreply@agileandco.com';
            $subject = 'New Contact Form Submission from ' . trim($name);

            $body = "New contact form submission:\n\n";
            foreach ($formFields as $field) {
                $val = trim($_POST[$field['field_key']] ?? '');
                if ($val !== '') {
                    $body .= $field['field_label'] . ": " . $val . "\n";
                }
            }
            $body .= "\nSubmitted: " . date('M j, Y g:i A');

            $headers = "From: " . trim($name) . " via Agile & Co <" . $emailFrom . ">\r\n";
            $headers .= "Reply-To: " . trim($name) . " <" . $emailFrom . ">\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            foreach ($notifyEmails as $to) {
                @mail($to, $subject, $body, $headers);
            }
        }

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Agile & Co</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="header" id="header">
        <div class="container">
            <div class="header-inner">
                <a href="index.php" class="logo">Agile & Co</a>
                <nav class="nav">
                    <ul class="nav-links">
                        <li><a href="service.php">Services</a></li>
                        <li><a href="industries.php">Industries</a></li>
                        <li><a href="core.php">Core</a></li>
                        <li><a href="about.php">About</a></li>
                    </ul>
                    <a href="contact.php" class="btn btn-primary">Let's Talk Growth</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content">
                <p class="pre-headline" style="justify-content: center;"><?= esc($pageContent, 'contact_hero', 'pre_headline', $pageDefaults['contact_hero']['pre_headline']) ?></p>
                <h1><?= sc($pageContent, 'contact_hero', 'headline', $pageDefaults['contact_hero']['headline']) ?></h1>
                <p class="hero-subtitle"><?= esc($pageContent, 'contact_hero', 'subtitle', $pageDefaults['contact_hero']['subtitle']) ?></p>
            </div>
        </div>
    </section>

    <section class="contact-section section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2><?= esc($pageContent, 'contact_info', 'heading', $pageDefaults['contact_info']['heading']) ?></h2>
                    <p><?= esc($pageContent, 'contact_info', 'description', $pageDefaults['contact_info']['description']) ?></p>
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="contact-method-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
                            <div class="contact-method-text"><h4><?= esc($pageContent, 'contact_info', 'email_label', $pageDefaults['contact_info']['email_label']) ?></h4><p><a href="mailto:<?= esc($pageContent, 'contact_info', 'email', $pageDefaults['contact_info']['email']) ?>"><?= esc($pageContent, 'contact_info', 'email', $pageDefaults['contact_info']['email']) ?></a></p></div>
                        </div>
                        <div class="contact-method">
                            <div class="contact-method-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg></div>
                            <div class="contact-method-text"><h4><?= esc($pageContent, 'contact_info', 'phone_label', $pageDefaults['contact_info']['phone_label']) ?></h4><p><a href="tel:<?= esc($pageContent, 'contact_info', 'phone_href', $pageDefaults['contact_info']['phone_href']) ?>"><?= esc($pageContent, 'contact_info', 'phone', $pageDefaults['contact_info']['phone']) ?></a></p></div>
                        </div>
                        <div class="contact-method">
                            <div class="contact-method-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                            <div class="contact-method-text"><h4><?= esc($pageContent, 'contact_info', 'response_label', $pageDefaults['contact_info']['response_label']) ?></h4><p><?= esc($pageContent, 'contact_info', 'response_text', $pageDefaults['contact_info']['response_text']) ?></p></div>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <?php if ($success): ?>
                        <div style="text-align: center; padding: 60px 20px;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--accent), var(--accent-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#000" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 style="font-size: 28px; margin-bottom: 16px;"><?= esc($pageContent, 'contact_success', 'heading', $pageDefaults['contact_success']['heading']) ?></h3>
                            <p style="color: var(--gray-400); font-size: 18px;"><?= esc($pageContent, 'contact_success', 'text', $pageDefaults['contact_success']['text']) ?></p>
                        </div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #ff6b6b; font-size: 14px;">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="contact.php">
                            <?php
                            $inRow = false;
                            $fieldCount = count($formFields);
                            for ($fi = 0; $fi < $fieldCount; $fi++):
                                $field = $formFields[$fi];
                                $key = $field['field_key'];
                                $label = htmlspecialchars($field['field_label']) . ($field['is_required'] ? ' *' : '');
                                $ph = htmlspecialchars($field['placeholder']);
                                $val = htmlspecialchars($_POST[$key] ?? '');
                                $req = $field['is_required'] ? ' required' : '';
                                $isHalf = $field['field_width'] === 'half';
                                $nextIsHalf = isset($formFields[$fi + 1]) && $formFields[$fi + 1]['field_width'] === 'half';

                                if ($isHalf && !$inRow):
                                    $inRow = true;
                            ?>
                            <div class="form-row">
                            <?php endif; ?>

                                <?php if ($field['field_type'] === 'select'): ?>
                                <div class="form-group">
                                    <label><?= $label ?></label>
                                    <select name="<?= htmlspecialchars($key) ?>">
                                        <option value=""><?= $ph ?: 'Select...' ?></option>
                                        <?php
                                        $options = $field['select_options'] ? json_decode($field['select_options'], true) : [];
                                        foreach ($options as $opt):
                                            $optVal = htmlspecialchars($opt['value'] ?? '');
                                            $optLabel = htmlspecialchars($opt['label'] ?? '');
                                            $selected = (($_POST[$key] ?? '') === ($opt['value'] ?? '')) ? ' selected' : '';
                                        ?>
                                            <option value="<?= $optVal ?>"<?= $selected ?>><?= $optLabel ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php elseif ($field['field_type'] === 'textarea'): ?>
                                <div class="form-group"><label><?= $label ?></label><textarea name="<?= htmlspecialchars($key) ?>" placeholder="<?= $ph ?>"<?= $req ?>><?= $val ?></textarea></div>
                                <?php else: ?>
                                <div class="form-group"><label><?= $label ?></label><input type="<?= htmlspecialchars($field['field_type']) ?>" name="<?= htmlspecialchars($key) ?>" placeholder="<?= $ph ?>"<?= $req ?> value="<?= $val ?>"></div>
                                <?php endif; ?>

                            <?php
                                if ($inRow && (!$nextIsHalf || $fi === $fieldCount - 1)):
                                    $inRow = false;
                            ?>
                            </div>
                            <?php endif; ?>

                            <?php endfor; ?>
                            <button type="submit" class="btn btn-primary form-submit">Send Message &rarr;</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand"><a href="index.php" class="logo">Agile & Co</a><p>AI-powered marketing for local service businesses.</p></div>
                <div class="footer-col"><h4>Services</h4><ul><li><a href="services-seo.php">SEO</a></li><li><a href="services-google-ads.php">Google Ads</a></li><li><a href="services-meta-ads.php">Meta Ads</a></li><li><a href="services-web-design.php">Website Design</a></li></ul></div>
                <div class="footer-col"><h4>Industries</h4><ul><li><a href="industry-hvac.php">HVAC</a></li><li><a href="industry-plumbing.php">Plumbing</a></li><li><a href="industry-electrical.php">Electrical</a></li><li><a href="industries.php">View All</a></li></ul></div>
                <div class="footer-col"><h4>Company</h4><ul><li><a href="about.php">About Us</a></li><li><a href="core.php">Core</a></li><li><a href="blog.php">Blog</a></li><li><a href="contact.php">Contact</a></li><li><a href="quiz.php">Free Quiz</a></li></ul></div>
            </div>
            <div class="footer-bottom"><p>&copy; 2025 Agile & Co. All rights reserved.</p><div class="footer-legal"><a href="#">Privacy Policy</a><a href="#">Terms of Service</a></div></div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/chatbot.js"></script>
</body>
</html>

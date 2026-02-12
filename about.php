<?php
require_once 'config/database.php';
$defaults = require 'config/about-defaults.php';
$rows = $pdo->query("SELECT section, field_key, field_value FROM site_content")->fetchAll();
$content = [];
foreach ($rows as $row) {
    $content[$row['section']][$row['field_key']] = $row['field_value'];
}
function sc($content, $section, $key, $default = '') {
    return $content[$section][$key] ?? $default;
}
function esc($content, $section, $key, $default = '') {
    return htmlspecialchars(sc($content, $section, $key, $default));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Agile & Co</title>
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
                        <li><a href="industry-hvac.php">Industries</a></li>
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
                <p class="pre-headline"><?= esc($content, 'about_hero', 'pre_headline', $defaults['about_hero']['pre_headline']) ?></p>
                <h1><?= sc($content, 'about_hero', 'headline', $defaults['about_hero']['headline']) ?></h1>
                <p class="hero-subtitle"><?= esc($content, 'about_hero', 'subtitle', $defaults['about_hero']['subtitle']) ?></p>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="story-section section">
        <div class="container">
            <div class="story-grid">
                <div class="story-content animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'about_story', 'pre_headline', $defaults['about_story']['pre_headline']) ?></p>
                    <h2><?= sc($content, 'about_story', 'headline', $defaults['about_story']['headline']) ?></h2>
                    <p><?= esc($content, 'about_story', 'paragraph1', $defaults['about_story']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'about_story', 'highlight', $defaults['about_story']['highlight']) ?></span></p>
                    <p><?= esc($content, 'about_story', 'paragraph2', $defaults['about_story']['paragraph2']) ?></p>
                </div>
                <div class="animate-on-scroll stagger-2" style="background: linear-gradient(135deg, var(--gray-800), var(--gray-900)); border: 1px solid var(--gray-700); border-radius: 20px; padding: 40px;">
                    <h3 style="font-size: 24px; margin-bottom: 24px;"><?= esc($content, 'about_story', 'focus_title', $defaults['about_story']['focus_title']) ?></h3>
                    <p style="color: var(--gray-400); line-height: 1.8;"><?= esc($content, 'about_story', 'focus_text', $defaults['about_story']['focus_text']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="values-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll" style="justify-content: center; text-align: center; display: flex;"><?= esc($content, 'about_values', 'pre_headline', $defaults['about_values']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= sc($content, 'about_values', 'headline', $defaults['about_values']['headline']) ?></h2>
            <div class="values-grid">
                <div class="value-card animate-on-scroll stagger-2"><h3><?= esc($content, 'about_values', 'card1_title', $defaults['about_values']['card1_title']) ?></h3><p><?= esc($content, 'about_values', 'card1_text', $defaults['about_values']['card1_text']) ?></p></div>
                <div class="value-card animate-on-scroll stagger-3"><h3><?= esc($content, 'about_values', 'card2_title', $defaults['about_values']['card2_title']) ?></h3><p><?= esc($content, 'about_values', 'card2_text', $defaults['about_values']['card2_text']) ?></p></div>
                <div class="value-card animate-on-scroll stagger-4"><h3><?= esc($content, 'about_values', 'card3_title', $defaults['about_values']['card3_title']) ?></h3><p><?= esc($content, 'about_values', 'card3_text', $defaults['about_values']['card3_text']) ?></p></div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="different-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'about_different', 'pre_headline', $defaults['about_different']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= sc($content, 'about_different', 'headline', $defaults['about_different']['headline']) ?></h2>
            <div class="different-list">
                <div class="different-item animate-on-scroll stagger-2"><h3><?= esc($content, 'about_different', 'item1_title', $defaults['about_different']['item1_title']) ?></h3><p><?= esc($content, 'about_different', 'item1_text', $defaults['about_different']['item1_text']) ?></p></div>
                <div class="different-item animate-on-scroll stagger-3"><h3><?= esc($content, 'about_different', 'item2_title', $defaults['about_different']['item2_title']) ?></h3><p><?= esc($content, 'about_different', 'item2_text', $defaults['about_different']['item2_text']) ?></p></div>
                <div class="different-item animate-on-scroll stagger-4"><h3><?= esc($content, 'about_different', 'item3_title', $defaults['about_different']['item3_title']) ?></h3><p><?= esc($content, 'about_different', 'item3_text', $defaults['about_different']['item3_text']) ?></p></div>
                <div class="different-item animate-on-scroll stagger-2"><h3><?= esc($content, 'about_different', 'item4_title', $defaults['about_different']['item4_title']) ?></h3><p><?= esc($content, 'about_different', 'item4_text', $defaults['about_different']['item4_text']) ?></p></div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'about_cta', 'headline', $defaults['about_cta']['headline']) ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, 'about_cta', 'subtitle', $defaults['about_cta']['subtitle']) ?></p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, 'about_cta', 'cta_primary', $defaults['about_cta']['cta_primary']) ?> →</a>
                <a href="core.php" class="btn btn-secondary"><?= esc($content, 'about_cta', 'cta_secondary', $defaults['about_cta']['cta_secondary']) ?></a>
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

<?php
require_once 'config/database.php';
$allDefaults = require 'config/services-defaults.php';
$defaults = $allDefaults['meta'];
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
    <title>Meta Ads Services | Agile & Co</title>
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
                <p class="pre-headline"><?= esc($content, 'meta_hero', 'pre_headline', $defaults['meta_hero']['pre_headline']) ?></p>
                <h1><?= esc($content, 'meta_hero', 'headline', $defaults['meta_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'meta_hero', 'headline_accent', $defaults['meta_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'meta_hero', 'subtitle', $defaults['meta_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'meta_hero', 'cta_primary', $defaults['meta_hero']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, 'meta_hero', 'cta_secondary', $defaults['meta_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>
    <div class="section-divider" style="background: var(--gray-900);"></div>
    <section class="content-section section">
        <div class="container">
            <div class="content-grid">
                <div class="content-text animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'meta_core_intro', 'pre_headline', $defaults['meta_core_intro']['pre_headline']) ?></p>
                    <h2><?= esc($content, 'meta_core_intro', 'headline', $defaults['meta_core_intro']['headline']) ?> <span class="text-accent"><?= esc($content, 'meta_core_intro', 'headline_accent', $defaults['meta_core_intro']['headline_accent']) ?></span></h2>
                    <p><?= esc($content, 'meta_core_intro', 'paragraph1', $defaults['meta_core_intro']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'meta_core_intro', 'paragraph2', $defaults['meta_core_intro']['paragraph2']) ?></span></p>
                    <p><?= esc($content, 'meta_core_intro', 'paragraph3', $defaults['meta_core_intro']['paragraph3']) ?></p>
                    <p><?= sc($content, 'meta_core_intro', 'paragraph4', $defaults['meta_core_intro']['paragraph4']) ?></p>
                </div>
                <div class="animate-on-scroll stagger-2">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                        </div>
                        <h3>Powered by Core</h3>
                        <p style="color: var(--gray-400);">Our proprietary AI engine for smarter social advertising</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="section-divider" style="background: var(--black);"></div>
    <section class="section" style="background: var(--black);">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'meta_services', 'pre_headline', $defaults['meta_services']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1" style="font-size: clamp(32px, 4vw, 48px); margin-bottom: 60px;"><?= esc($content, 'meta_services', 'headline', $defaults['meta_services']['headline']) ?> <span class="text-accent"><?= esc($content, 'meta_services', 'headline_accent', $defaults['meta_services']['headline_accent']) ?></span></h2>
            <div class="services-grid">
                <div class="service-item animate-on-scroll stagger-1"><h3><?= esc($content, 'meta_services', 'card1_title', $defaults['meta_services']['card1_title']) ?></h3><p><?= esc($content, 'meta_services', 'card1_text', $defaults['meta_services']['card1_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-2"><h3><?= esc($content, 'meta_services', 'card2_title', $defaults['meta_services']['card2_title']) ?></h3><p><?= esc($content, 'meta_services', 'card2_text', $defaults['meta_services']['card2_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-3"><h3><?= esc($content, 'meta_services', 'card3_title', $defaults['meta_services']['card3_title']) ?></h3><p><?= esc($content, 'meta_services', 'card3_text', $defaults['meta_services']['card3_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-4"><h3><?= esc($content, 'meta_services', 'card4_title', $defaults['meta_services']['card4_title']) ?></h3><p><?= esc($content, 'meta_services', 'card4_text', $defaults['meta_services']['card4_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-5"><h3><?= esc($content, 'meta_services', 'card5_title', $defaults['meta_services']['card5_title']) ?></h3><p><?= esc($content, 'meta_services', 'card5_text', $defaults['meta_services']['card5_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-6"><h3><?= esc($content, 'meta_services', 'card6_title', $defaults['meta_services']['card6_title']) ?></h3><p><?= esc($content, 'meta_services', 'card6_text', $defaults['meta_services']['card6_text']) ?></p></div>
            </div>
        </div>
    </section>
    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'meta_cta', 'headline', $defaults['meta_cta']['headline']) ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, 'meta_cta', 'subtitle', $defaults['meta_cta']['subtitle']) ?></p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, 'meta_cta', 'cta_primary', $defaults['meta_cta']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'meta_cta', 'cta_secondary', $defaults['meta_cta']['cta_secondary']) ?></a>
            </div>
        </div>
    </section>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand"><a href="index.php" class="logo">Agile & Co</a><p>AI-powered marketing for local service businesses. More leads. More booked jobs. Zero guesswork.</p></div>
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

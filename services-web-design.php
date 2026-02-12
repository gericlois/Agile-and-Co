<?php
require_once 'config/database.php';
$allDefaults = require 'config/services-defaults.php';
$defaults = $allDefaults['webdesign'];
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
    <title>Website Design Services | Agile & Co</title>
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
                <p class="pre-headline"><?= esc($content, 'webdesign_hero', 'pre_headline', $defaults['webdesign_hero']['pre_headline']) ?></p>
                <h1><?= esc($content, 'webdesign_hero', 'headline', $defaults['webdesign_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'webdesign_hero', 'headline_accent', $defaults['webdesign_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'webdesign_hero', 'subtitle', $defaults['webdesign_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'webdesign_hero', 'cta_primary', $defaults['webdesign_hero']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="#process" class="btn btn-secondary"><?= esc($content, 'webdesign_hero', 'cta_secondary', $defaults['webdesign_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="content-section section">
        <div class="container">
            <div class="content-grid">
                <div class="content-text animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'webdesign_core_intro', 'pre_headline', $defaults['webdesign_core_intro']['pre_headline']) ?></p>
                    <h2><?= esc($content, 'webdesign_core_intro', 'headline', $defaults['webdesign_core_intro']['headline']) ?> <span class="text-accent"><?= esc($content, 'webdesign_core_intro', 'headline_accent', $defaults['webdesign_core_intro']['headline_accent']) ?></span></h2>
                    <p><?= esc($content, 'webdesign_core_intro', 'paragraph1', $defaults['webdesign_core_intro']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'webdesign_core_intro', 'paragraph2', $defaults['webdesign_core_intro']['paragraph2']) ?></span></p>
                    <p><?= sc($content, 'webdesign_core_intro', 'paragraph3', $defaults['webdesign_core_intro']['paragraph3']) ?></p>
                    <p><?= sc($content, 'webdesign_core_intro', 'paragraph4', $defaults['webdesign_core_intro']['paragraph4']) ?></p>
                </div>
                <div class="animate-on-scroll stagger-2">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <h3>Built for Conversions</h3>
                        <p style="color: var(--gray-400);">Every website designed to turn visitors into booked jobs</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="section" style="background: var(--black);">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'webdesign_services', 'pre_headline', $defaults['webdesign_services']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1" style="font-size: clamp(32px, 4vw, 48px); margin-bottom: 60px;"><?= esc($content, 'webdesign_services', 'headline', $defaults['webdesign_services']['headline']) ?> <span class="text-accent"><?= esc($content, 'webdesign_services', 'headline_accent', $defaults['webdesign_services']['headline_accent']) ?></span></h2>
            <div class="services-grid">
                <div class="service-item animate-on-scroll stagger-1"><h3><?= esc($content, 'webdesign_services', 'card1_title', $defaults['webdesign_services']['card1_title']) ?></h3><p><?= esc($content, 'webdesign_services', 'card1_text', $defaults['webdesign_services']['card1_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-2"><h3><?= esc($content, 'webdesign_services', 'card2_title', $defaults['webdesign_services']['card2_title']) ?></h3><p><?= esc($content, 'webdesign_services', 'card2_text', $defaults['webdesign_services']['card2_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-3"><h3><?= esc($content, 'webdesign_services', 'card3_title', $defaults['webdesign_services']['card3_title']) ?></h3><p><?= esc($content, 'webdesign_services', 'card3_text', $defaults['webdesign_services']['card3_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-4"><h3><?= esc($content, 'webdesign_services', 'card4_title', $defaults['webdesign_services']['card4_title']) ?></h3><p><?= esc($content, 'webdesign_services', 'card4_text', $defaults['webdesign_services']['card4_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-5"><h3><?= esc($content, 'webdesign_services', 'card5_title', $defaults['webdesign_services']['card5_title']) ?></h3><p><?= esc($content, 'webdesign_services', 'card5_text', $defaults['webdesign_services']['card5_text']) ?></p></div>
                <div class="service-item animate-on-scroll stagger-6"><h3><?= esc($content, 'webdesign_services', 'card6_title', $defaults['webdesign_services']['card6_title']) ?></h3><p><?= esc($content, 'webdesign_services', 'card6_text', $defaults['webdesign_services']['card6_text']) ?></p></div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);" id="process"></div>

    <section class="section" style="background: linear-gradient(180deg, var(--gray-900) 0%, var(--black) 100%);">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'webdesign_timeline', 'pre_headline', $defaults['webdesign_timeline']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1" style="font-size: clamp(32px, 4vw, 48px); margin-bottom: 60px;"><?= esc($content, 'webdesign_timeline', 'headline', $defaults['webdesign_timeline']['headline']) ?> <span class="text-accent"><?= esc($content, 'webdesign_timeline', 'headline_accent', $defaults['webdesign_timeline']['headline_accent']) ?></span></h2>
            <div class="timeline">
                <div class="timeline-item animate-on-scroll stagger-2"><div class="timeline-period"><?= esc($content, 'webdesign_timeline', 'step1_period', $defaults['webdesign_timeline']['step1_period']) ?></div><div><h3><?= esc($content, 'webdesign_timeline', 'step1_title', $defaults['webdesign_timeline']['step1_title']) ?></h3><p><?= esc($content, 'webdesign_timeline', 'step1_text', $defaults['webdesign_timeline']['step1_text']) ?></p></div></div>
                <div class="timeline-item animate-on-scroll stagger-3"><div class="timeline-period"><?= esc($content, 'webdesign_timeline', 'step2_period', $defaults['webdesign_timeline']['step2_period']) ?></div><div><h3><?= esc($content, 'webdesign_timeline', 'step2_title', $defaults['webdesign_timeline']['step2_title']) ?></h3><p><?= esc($content, 'webdesign_timeline', 'step2_text', $defaults['webdesign_timeline']['step2_text']) ?></p></div></div>
                <div class="timeline-item animate-on-scroll stagger-4"><div class="timeline-period"><?= esc($content, 'webdesign_timeline', 'step3_period', $defaults['webdesign_timeline']['step3_period']) ?></div><div><h3><?= esc($content, 'webdesign_timeline', 'step3_title', $defaults['webdesign_timeline']['step3_title']) ?></h3><p><?= esc($content, 'webdesign_timeline', 'step3_text', $defaults['webdesign_timeline']['step3_text']) ?></p></div></div>
                <div class="timeline-item animate-on-scroll stagger-5"><div class="timeline-period"><?= esc($content, 'webdesign_timeline', 'step4_period', $defaults['webdesign_timeline']['step4_period']) ?></div><div><h3><?= esc($content, 'webdesign_timeline', 'step4_title', $defaults['webdesign_timeline']['step4_title']) ?></h3><p><?= esc($content, 'webdesign_timeline', 'step4_text', $defaults['webdesign_timeline']['step4_text']) ?></p></div></div>
                <div class="timeline-item animate-on-scroll stagger-6"><div class="timeline-period"><?= esc($content, 'webdesign_timeline', 'step5_period', $defaults['webdesign_timeline']['step5_period']) ?></div><div><h3><?= esc($content, 'webdesign_timeline', 'step5_title', $defaults['webdesign_timeline']['step5_title']) ?></h3><p><?= esc($content, 'webdesign_timeline', 'step5_text', $defaults['webdesign_timeline']['step5_text']) ?></p></div></div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'webdesign_cta', 'headline', $defaults['webdesign_cta']['headline']) ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, 'webdesign_cta', 'subtitle', $defaults['webdesign_cta']['subtitle']) ?></p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, 'webdesign_cta', 'cta_primary', $defaults['webdesign_cta']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'webdesign_cta', 'cta_secondary', $defaults['webdesign_cta']['cta_secondary']) ?></a>
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

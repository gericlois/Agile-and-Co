<?php
require_once 'config/database.php';
$allDefaults = require 'config/industries-defaults.php';
$defaults = $allDefaults['automotive'];
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
    <title>Automotive Repair Marketing | Agile & Co</title>
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
                <div class="hero-badge"><?= esc($content, 'automotive_hero', 'badge', $defaults['automotive_hero']['badge']) ?></div>
                <h1><?= esc($content, 'automotive_hero', 'headline', $defaults['automotive_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'automotive_hero', 'headline_accent', $defaults['automotive_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'automotive_hero', 'subtitle', $defaults['automotive_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'automotive_hero', 'cta_primary', $defaults['automotive_hero']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, 'automotive_hero', 'cta_secondary', $defaults['automotive_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="intro-section section">
        <div class="container">
            <div class="intro-grid">
                <div class="intro-content animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'automotive_intro', 'pre_headline', $defaults['automotive_intro']['pre_headline']) ?></p>
                    <h2><?= esc($content, 'automotive_intro', 'headline', $defaults['automotive_intro']['headline']) ?> <span class="text-accent"><?= esc($content, 'automotive_intro', 'headline_accent', $defaults['automotive_intro']['headline_accent']) ?></span></h2>
                    <p><?= esc($content, 'automotive_intro', 'paragraph1', $defaults['automotive_intro']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'automotive_intro', 'paragraph2', $defaults['automotive_intro']['paragraph2']) ?></span></p>
                    <p><?= esc($content, 'automotive_intro', 'paragraph3', $defaults['automotive_intro']['paragraph3']) ?></p>
                </div>
                <div class="animate-on-scroll stagger-2">
                    <div class="intro-card">
                        <h3><?= esc($content, 'automotive_intro', 'card_title', $defaults['automotive_intro']['card_title']) ?></h3>
                        <p><?= esc($content, 'automotive_intro', 'card_text', $defaults['automotive_intro']['card_text']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="challenges section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'automotive_challenges', 'pre_headline', $defaults['automotive_challenges']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'automotive_challenges', 'headline', $defaults['automotive_challenges']['headline']) ?> <span class="text-accent"><?= esc($content, 'automotive_challenges', 'headline_accent', $defaults['automotive_challenges']['headline_accent']) ?></span></h2>
            <div class="challenges-grid">
                <div class="challenge-card animate-on-scroll stagger-2">
                    <div class="challenge-number">01</div>
                    <h3><?= esc($content, 'automotive_challenges', 'card1_title', $defaults['automotive_challenges']['card1_title']) ?></h3>
                    <p><?= esc($content, 'automotive_challenges', 'card1_text', $defaults['automotive_challenges']['card1_text']) ?></p>
                    <div class="challenge-solution"><strong>How We Help</strong><p><?= esc($content, 'automotive_challenges', 'card1_solution', $defaults['automotive_challenges']['card1_solution']) ?></p></div>
                </div>
                <div class="challenge-card animate-on-scroll stagger-3">
                    <div class="challenge-number">02</div>
                    <h3><?= esc($content, 'automotive_challenges', 'card2_title', $defaults['automotive_challenges']['card2_title']) ?></h3>
                    <p><?= esc($content, 'automotive_challenges', 'card2_text', $defaults['automotive_challenges']['card2_text']) ?></p>
                    <div class="challenge-solution"><strong>How We Help</strong><p><?= esc($content, 'automotive_challenges', 'card2_solution', $defaults['automotive_challenges']['card2_solution']) ?></p></div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="section" style="background: linear-gradient(180deg, var(--gray-900) 0%, var(--black) 100%);">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'automotive_services', 'pre_headline', $defaults['automotive_services']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1" style="font-size: clamp(32px, 4vw, 48px); margin-bottom: 60px;"><?= esc($content, 'automotive_services', 'headline', $defaults['automotive_services']['headline']) ?> <span class="text-accent"><?= esc($content, 'automotive_services', 'headline_accent', $defaults['automotive_services']['headline_accent']) ?></span></h2>
            <div class="services-grid">
                <div class="service-card animate-on-scroll stagger-2"><h3><?= esc($content, 'automotive_services', 'card1_title', $defaults['automotive_services']['card1_title']) ?></h3><p><?= esc($content, 'automotive_services', 'card1_text', $defaults['automotive_services']['card1_text']) ?></p><a href="services-seo.php" class="service-card-link">Learn More &rarr;</a></div>
                <div class="service-card animate-on-scroll stagger-3"><h3><?= esc($content, 'automotive_services', 'card2_title', $defaults['automotive_services']['card2_title']) ?></h3><p><?= esc($content, 'automotive_services', 'card2_text', $defaults['automotive_services']['card2_text']) ?></p><a href="services-google-ads.php" class="service-card-link">Learn More &rarr;</a></div>
                <div class="service-card animate-on-scroll stagger-4"><h3><?= esc($content, 'automotive_services', 'card3_title', $defaults['automotive_services']['card3_title']) ?></h3><p><?= esc($content, 'automotive_services', 'card3_text', $defaults['automotive_services']['card3_text']) ?></p><a href="services-meta-ads.php" class="service-card-link">Learn More &rarr;</a></div>
                <div class="service-card animate-on-scroll stagger-2"><h3><?= esc($content, 'automotive_services', 'card4_title', $defaults['automotive_services']['card4_title']) ?></h3><p><?= esc($content, 'automotive_services', 'card4_text', $defaults['automotive_services']['card4_text']) ?></p><a href="services-web-design.php" class="service-card-link">Learn More &rarr;</a></div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2><?= esc($content, 'automotive_cta', 'headline', $defaults['automotive_cta']['headline']) ?></h2>
            <p><?= esc($content, 'automotive_cta', 'subtitle', $defaults['automotive_cta']['subtitle']) ?></p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, 'automotive_cta', 'cta_primary', $defaults['automotive_cta']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'automotive_cta', 'cta_secondary', $defaults['automotive_cta']['cta_secondary']) ?></a>
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

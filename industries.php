<?php
require_once 'config/database.php';
$allDefaults = require 'config/industries-defaults.php';
$defaults = $allDefaults['listing'];
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
    <title>Industries We Serve | Agile & Co</title>
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
        <div class="hero-grid"></div>
        <div class="container">
            <div class="hero-content">
                <h1><?= esc($content, 'listing_hero', 'headline', $defaults['listing_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'listing_hero', 'headline_accent', $defaults['listing_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'listing_hero', 'subtitle', $defaults['listing_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'listing_hero', 'cta_primary', $defaults['listing_hero']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="industries-section section">
        <div class="container">
            <h2 class="animate-on-scroll">Find Your Industry</h2>
            <div class="industry-cards-grid">
                <a href="industry-hvac.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card1_title', $defaults['listing_grid']['card1_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card1_text', $defaults['listing_grid']['card1_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-plumbing.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card2_title', $defaults['listing_grid']['card2_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card2_text', $defaults['listing_grid']['card2_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-electrical.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card3_title', $defaults['listing_grid']['card3_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card3_text', $defaults['listing_grid']['card3_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-remodeling.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card4_title', $defaults['listing_grid']['card4_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card4_text', $defaults['listing_grid']['card4_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-moving.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card5_title', $defaults['listing_grid']['card5_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card5_text', $defaults['listing_grid']['card5_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-deck.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card6_title', $defaults['listing_grid']['card6_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card6_text', $defaults['listing_grid']['card6_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-landscaping.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card7_title', $defaults['listing_grid']['card7_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card7_text', $defaults['listing_grid']['card7_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-general-contractors.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card8_title', $defaults['listing_grid']['card8_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card8_text', $defaults['listing_grid']['card8_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-automotive.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card9_title', $defaults['listing_grid']['card9_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card9_text', $defaults['listing_grid']['card9_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-medspa.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card10_title', $defaults['listing_grid']['card10_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card10_text', $defaults['listing_grid']['card10_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
                <a href="industry-fencing.php" class="industry-card animate-on-scroll"><h3><?= esc($content, 'listing_grid', 'card11_title', $defaults['listing_grid']['card11_title']) ?></h3><p><?= esc($content, 'listing_grid', 'card11_text', $defaults['listing_grid']['card11_text']) ?></p><span class="industry-card-link">Learn More →</span></a>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2><?= esc($content, 'listing_cta', 'headline', $defaults['listing_cta']['headline']) ?></h2>
            <p><?= esc($content, 'listing_cta', 'subtitle', $defaults['listing_cta']['subtitle']) ?></p>
            <a href="contact.php" class="btn btn-primary"><?= esc($content, 'listing_cta', 'cta_primary', $defaults['listing_cta']['cta_primary']) ?> →</a>
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

<?php
require_once 'config/database.php';
$defaults = require 'config/core-defaults.php';
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
    <title>Core - Our AI Engine | Agile & Co</title>
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
        <div class="hero-grid"></div>
        <div class="container">
            <div class="hero-content">
                <div class="core-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <p class="pre-headline" style="justify-content: center;"><?= esc($content, 'core_hero', 'pre_headline', $defaults['core_hero']['pre_headline']) ?></p>
                <h1><?= sc($content, 'core_hero', 'headline', $defaults['core_hero']['headline']) ?></h1>
                <p class="hero-subtitle"><?= esc($content, 'core_hero', 'subtitle', $defaults['core_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'core_hero', 'cta_primary', $defaults['core_hero']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                    <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'core_hero', 'cta_secondary', $defaults['core_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="problem-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'core_problem', 'pre_headline', $defaults['core_problem']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= sc($content, 'core_problem', 'headline', $defaults['core_problem']['headline']) ?></h2>
            <div class="problem-timeline">
                <div class="problem-item animate-on-scroll stagger-2"><div class="problem-month"><?= esc($content, 'core_problem', 'month1_label', $defaults['core_problem']['month1_label']) ?></div><p><?= esc($content, 'core_problem', 'month1_text', $defaults['core_problem']['month1_text']) ?></p></div>
                <div class="problem-item animate-on-scroll stagger-3"><div class="problem-month"><?= esc($content, 'core_problem', 'month2_label', $defaults['core_problem']['month2_label']) ?></div><p><?= esc($content, 'core_problem', 'month2_text', $defaults['core_problem']['month2_text']) ?></p></div>
                <div class="problem-item animate-on-scroll stagger-4"><div class="problem-month"><?= esc($content, 'core_problem', 'month3_label', $defaults['core_problem']['month3_label']) ?></div><p><?= esc($content, 'core_problem', 'month3_text', $defaults['core_problem']['month3_text']) ?></p></div>
                <div class="problem-item animate-on-scroll stagger-5"><div class="problem-month"><?= esc($content, 'core_problem', 'month4_label', $defaults['core_problem']['month4_label']) ?></div><p><?= esc($content, 'core_problem', 'month4_text', $defaults['core_problem']['month4_text']) ?></p></div>
            </div>
            <div class="problem-conclusion animate-on-scroll">
                <p><?= esc($content, 'core_problem', 'conclusion_text', $defaults['core_problem']['conclusion_text']) ?></p>
                <strong><?= esc($content, 'core_problem', 'conclusion_bold', $defaults['core_problem']['conclusion_bold']) ?></strong>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="features-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'core_features', 'pre_headline', $defaults['core_features']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= sc($content, 'core_features', 'headline', $defaults['core_features']['headline']) ?></h2>
            <p class="features-intro animate-on-scroll stagger-2"><?= esc($content, 'core_features', 'intro', $defaults['core_features']['intro']) ?></p>

            <div class="feature-block animate-on-scroll">
                <div class="feature-content">
                    <div class="feature-tech"><?= esc($content, 'core_features', 'f1_tech', $defaults['core_features']['f1_tech']) ?></div>
                    <h3><?= esc($content, 'core_features', 'f1_title', $defaults['core_features']['f1_title']) ?></h3>
                    <p><?= esc($content, 'core_features', 'f1_text1', $defaults['core_features']['f1_text1']) ?></p>
                    <p><?= esc($content, 'core_features', 'f1_text2', $defaults['core_features']['f1_text2']) ?></p>
                    <div class="feature-example">
                        <strong>Example</strong>
                        <p><?= esc($content, 'core_features', 'f1_example', $defaults['core_features']['f1_example']) ?></p>
                    </div>
                </div>
                <div class="feature-visual">
                    <div class="feature-icon-large"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg></div>
                </div>
            </div>

            <div class="feature-block animate-on-scroll">
                <div class="feature-content">
                    <div class="feature-tech"><?= esc($content, 'core_features', 'f2_tech', $defaults['core_features']['f2_tech']) ?></div>
                    <h3><?= esc($content, 'core_features', 'f2_title', $defaults['core_features']['f2_title']) ?></h3>
                    <p><?= esc($content, 'core_features', 'f2_text1', $defaults['core_features']['f2_text1']) ?></p>
                    <p><?= esc($content, 'core_features', 'f2_text2', $defaults['core_features']['f2_text2']) ?></p>
                    <div class="feature-example">
                        <strong>Example</strong>
                        <p><?= esc($content, 'core_features', 'f2_example', $defaults['core_features']['f2_example']) ?></p>
                    </div>
                </div>
                <div class="feature-visual">
                    <div class="feature-icon-large"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
                </div>
            </div>

            <div class="feature-block animate-on-scroll">
                <div class="feature-content">
                    <div class="feature-tech"><?= esc($content, 'core_features', 'f3_tech', $defaults['core_features']['f3_tech']) ?></div>
                    <h3><?= esc($content, 'core_features', 'f3_title', $defaults['core_features']['f3_title']) ?></h3>
                    <p><?= esc($content, 'core_features', 'f3_text1', $defaults['core_features']['f3_text1']) ?></p>
                    <p><?= esc($content, 'core_features', 'f3_text2', $defaults['core_features']['f3_text2']) ?></p>
                    <div class="feature-example">
                        <strong>Example</strong>
                        <p><?= esc($content, 'core_features', 'f3_example', $defaults['core_features']['f3_example']) ?></p>
                    </div>
                </div>
                <div class="feature-visual">
                    <div class="feature-icon-large"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg></div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="results-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'core_results', 'pre_headline', $defaults['core_results']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= sc($content, 'core_results', 'headline', $defaults['core_results']['headline']) ?></h2>
            <div class="results-grid">
                <div class="result-card animate-on-scroll stagger-2"><h3><?= esc($content, 'core_results', 'card1_title', $defaults['core_results']['card1_title']) ?></h3><p><?= esc($content, 'core_results', 'card1_text', $defaults['core_results']['card1_text']) ?></p></div>
                <div class="result-card animate-on-scroll stagger-3"><h3><?= esc($content, 'core_results', 'card2_title', $defaults['core_results']['card2_title']) ?></h3><p><?= esc($content, 'core_results', 'card2_text', $defaults['core_results']['card2_text']) ?></p></div>
                <div class="result-card animate-on-scroll stagger-4"><h3><?= esc($content, 'core_results', 'card3_title', $defaults['core_results']['card3_title']) ?></h3><p><?= esc($content, 'core_results', 'card3_text', $defaults['core_results']['card3_text']) ?></p></div>
                <div class="result-card animate-on-scroll stagger-5"><h3><?= esc($content, 'core_results', 'card4_title', $defaults['core_results']['card4_title']) ?></h3><p><?= esc($content, 'core_results', 'card4_text', $defaults['core_results']['card4_text']) ?></p></div>
                <div class="result-card animate-on-scroll stagger-2"><h3><?= esc($content, 'core_results', 'card5_title', $defaults['core_results']['card5_title']) ?></h3><p><?= esc($content, 'core_results', 'card5_text', $defaults['core_results']['card5_text']) ?></p></div>
                <div class="result-card animate-on-scroll stagger-3"><h3><?= esc($content, 'core_results', 'card6_title', $defaults['core_results']['card6_title']) ?></h3><p><?= esc($content, 'core_results', 'card6_text', $defaults['core_results']['card6_text']) ?></p></div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="faq-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'core_faq', 'pre_headline', $defaults['core_faq']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= sc($content, 'core_faq', 'headline', $defaults['core_faq']['headline']) ?></h2>
            <div class="faq-list">
                <div class="faq-item animate-on-scroll stagger-2">
                    <div class="faq-question"><h3><?= esc($content, 'core_faq', 'q1', $defaults['core_faq']['q1']) ?></h3><svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg></div>
                    <div class="faq-answer"><p><?= esc($content, 'core_faq', 'a1', $defaults['core_faq']['a1']) ?></p></div>
                </div>
                <div class="faq-item animate-on-scroll stagger-3">
                    <div class="faq-question"><h3><?= esc($content, 'core_faq', 'q2', $defaults['core_faq']['q2']) ?></h3><svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg></div>
                    <div class="faq-answer"><p><?= esc($content, 'core_faq', 'a2', $defaults['core_faq']['a2']) ?></p></div>
                </div>
                <div class="faq-item animate-on-scroll stagger-4">
                    <div class="faq-question"><h3><?= esc($content, 'core_faq', 'q3', $defaults['core_faq']['q3']) ?></h3><svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg></div>
                    <div class="faq-answer"><p><?= esc($content, 'core_faq', 'a3', $defaults['core_faq']['a3']) ?></p></div>
                </div>
                <div class="faq-item animate-on-scroll stagger-5">
                    <div class="faq-question"><h3><?= esc($content, 'core_faq', 'q4', $defaults['core_faq']['q4']) ?></h3><svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg></div>
                    <div class="faq-answer"><p><?= esc($content, 'core_faq', 'a4', $defaults['core_faq']['a4']) ?></p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'core_cta', 'headline', $defaults['core_cta']['headline']) ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, 'core_cta', 'subtitle', $defaults['core_cta']['subtitle']) ?></p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, 'core_cta', 'cta_primary', $defaults['core_cta']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'core_cta', 'cta_secondary', $defaults['core_cta']['cta_secondary']) ?></a>
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

<?php
require_once 'config/database.php';
$allDefaults = require 'config/services-defaults.php';
$defaults = $allDefaults['gads'];
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
    <title>Google Ads Services | Agile & Co</title>
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
                <p class="pre-headline"><?= esc($content, 'gads_hero', 'pre_headline', $defaults['gads_hero']['pre_headline']) ?></p>
                <h1><?= esc($content, 'gads_hero', 'headline', $defaults['gads_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'gads_hero', 'headline_accent', $defaults['gads_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'gads_hero', 'subtitle', $defaults['gads_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'gads_hero', 'cta_primary', $defaults['gads_hero']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, 'gads_hero', 'cta_secondary', $defaults['gads_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="core-intro section">
        <div class="container">
            <div class="core-intro-grid">
                <div class="core-intro-content animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'gads_core_intro', 'pre_headline', $defaults['gads_core_intro']['pre_headline']) ?></p>
                    <h2><?= esc($content, 'gads_core_intro', 'headline', $defaults['gads_core_intro']['headline']) ?> <span class="text-accent"><?= esc($content, 'gads_core_intro', 'headline_accent', $defaults['gads_core_intro']['headline_accent']) ?></span></h2>
                    <p><?= esc($content, 'gads_core_intro', 'paragraph1', $defaults['gads_core_intro']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'gads_core_intro', 'paragraph2', $defaults['gads_core_intro']['paragraph2']) ?></span></p>
                    <p><?= esc($content, 'gads_core_intro', 'paragraph3', $defaults['gads_core_intro']['paragraph3']) ?></p>
                    <p><?= sc($content, 'gads_core_intro', 'paragraph4', $defaults['gads_core_intro']['paragraph4']) ?></p>
                </div>
                <div class="core-intro-visual animate-on-scroll stagger-2">
                    <div class="core-badge">
                        <div class="core-badge-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3>Powered by Core</h3>
                        <p>Our proprietary AI engine</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="problem section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'gads_comparison', 'headline', $defaults['gads_comparison']['headline']) ?> <span class="text-accent"><?= esc($content, 'gads_comparison', 'headline_accent', $defaults['gads_comparison']['headline_accent']) ?></span></h2>
            <div class="comparison-grid">
                <div class="comparison-card bad animate-on-scroll stagger-1">
                    <span class="comparison-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                        <?= esc($content, 'gads_comparison', 'bad_label', $defaults['gads_comparison']['bad_label']) ?>
                    </span>
                    <ul class="comparison-list">
                        <li><?= esc($content, 'gads_comparison', 'bad1', $defaults['gads_comparison']['bad1']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'bad2', $defaults['gads_comparison']['bad2']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'bad3', $defaults['gads_comparison']['bad3']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'bad4', $defaults['gads_comparison']['bad4']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'bad5', $defaults['gads_comparison']['bad5']) ?></li>
                    </ul>
                </div>
                <div class="comparison-card good animate-on-scroll stagger-2">
                    <span class="comparison-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <?= esc($content, 'gads_comparison', 'good_label', $defaults['gads_comparison']['good_label']) ?>
                    </span>
                    <ul class="comparison-list">
                        <li><?= esc($content, 'gads_comparison', 'good1', $defaults['gads_comparison']['good1']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'good2', $defaults['gads_comparison']['good2']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'good3', $defaults['gads_comparison']['good3']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'good4', $defaults['gads_comparison']['good4']) ?></li>
                        <li><?= esc($content, 'gads_comparison', 'good5', $defaults['gads_comparison']['good5']) ?></li>
                    </ul>
                </div>
            </div>
            <div class="comparison-bottom animate-on-scroll stagger-3">
                <p><?= sc($content, 'gads_comparison', 'bottom_text', $defaults['gads_comparison']['bottom_text']) ?></p>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="process-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'gads_process', 'pre_headline', $defaults['gads_process']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'gads_process', 'headline', $defaults['gads_process']['headline']) ?> <span class="text-accent"><?= esc($content, 'gads_process', 'headline_accent', $defaults['gads_process']['headline_accent']) ?></span></h2>
            <div class="process-timeline">
                <div class="process-item animate-on-scroll stagger-2">
                    <div class="process-number">1</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'gads_process', 'step1_title', $defaults['gads_process']['step1_title']) ?></h3>
                        <p><?= esc($content, 'gads_process', 'step1_text', $defaults['gads_process']['step1_text']) ?></p>
                        <div class="process-details">
                            <span class="process-tag">Account Audit</span>
                            <span class="process-tag">Competitor Analysis</span>
                            <span class="process-tag">Keyword Research</span>
                        </div>
                    </div>
                </div>
                <div class="process-item animate-on-scroll stagger-3">
                    <div class="process-number">2</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'gads_process', 'step2_title', $defaults['gads_process']['step2_title']) ?></h3>
                        <p><?= esc($content, 'gads_process', 'step2_text', $defaults['gads_process']['step2_text']) ?></p>
                        <div class="process-details">
                            <span class="process-tag">Search Campaigns</span>
                            <span class="process-tag">Local Service Ads</span>
                            <span class="process-tag">Remarketing</span>
                            <span class="process-tag">Landing Pages</span>
                        </div>
                    </div>
                </div>
                <div class="process-item animate-on-scroll stagger-4">
                    <div class="process-number">3</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'gads_process', 'step3_title', $defaults['gads_process']['step3_title']) ?></h3>
                        <p><?= esc($content, 'gads_process', 'step3_text', $defaults['gads_process']['step3_text']) ?></p>
                        <div class="process-details">
                            <span class="process-tag">Real-time Bid Adjustments</span>
                            <span class="process-tag">A/B Testing</span>
                            <span class="process-tag">Waste Elimination</span>
                        </div>
                    </div>
                </div>
                <div class="process-item animate-on-scroll stagger-5">
                    <div class="process-number">4</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'gads_process', 'step4_title', $defaults['gads_process']['step4_title']) ?></h3>
                        <p><?= esc($content, 'gads_process', 'step4_text', $defaults['gads_process']['step4_text']) ?></p>
                        <div class="process-details">
                            <span class="process-tag">Full Attribution</span>
                            <span class="process-tag">Revenue Tracking</span>
                            <span class="process-tag">Scale Winners</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="services-included section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'gads_services', 'pre_headline', $defaults['gads_services']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'gads_services', 'headline', $defaults['gads_services']['headline']) ?> <span class="text-accent"><?= esc($content, 'gads_services', 'headline_accent', $defaults['gads_services']['headline_accent']) ?></span></h2>
            <div class="services-grid">
                <div class="service-item animate-on-scroll stagger-1">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3><?= esc($content, 'gads_services', 'card1_title', $defaults['gads_services']['card1_title']) ?></h3>
                    <p><?= esc($content, 'gads_services', 'card1_text', $defaults['gads_services']['card1_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-2">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3><?= esc($content, 'gads_services', 'card2_title', $defaults['gads_services']['card2_title']) ?></h3>
                    <p><?= esc($content, 'gads_services', 'card2_text', $defaults['gads_services']['card2_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-3">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                    </div>
                    <h3><?= esc($content, 'gads_services', 'card3_title', $defaults['gads_services']['card3_title']) ?></h3>
                    <p><?= esc($content, 'gads_services', 'card3_text', $defaults['gads_services']['card3_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-4">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <h3><?= esc($content, 'gads_services', 'card4_title', $defaults['gads_services']['card4_title']) ?></h3>
                    <p><?= esc($content, 'gads_services', 'card4_text', $defaults['gads_services']['card4_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-5">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <h3><?= esc($content, 'gads_services', 'card5_title', $defaults['gads_services']['card5_title']) ?></h3>
                    <p><?= esc($content, 'gads_services', 'card5_text', $defaults['gads_services']['card5_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-6">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h3><?= esc($content, 'gads_services', 'card6_title', $defaults['gads_services']['card6_title']) ?></h3>
                    <p><?= esc($content, 'gads_services', 'card6_text', $defaults['gads_services']['card6_text']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="faq section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'gads_faq', 'pre_headline', $defaults['gads_faq']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'gads_faq', 'headline', $defaults['gads_faq']['headline']) ?> <span class="text-accent"><?= esc($content, 'gads_faq', 'headline_accent', $defaults['gads_faq']['headline_accent']) ?></span></h2>
            <div class="faq-list">
                <div class="faq-item animate-on-scroll stagger-2">
                    <div class="faq-question">
                        <h3><?= esc($content, 'gads_faq', 'q1', $defaults['gads_faq']['q1']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'gads_faq', 'a1', $defaults['gads_faq']['a1']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-3">
                    <div class="faq-question">
                        <h3><?= esc($content, 'gads_faq', 'q2', $defaults['gads_faq']['q2']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'gads_faq', 'a2', $defaults['gads_faq']['a2']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-4">
                    <div class="faq-question">
                        <h3><?= esc($content, 'gads_faq', 'q3', $defaults['gads_faq']['q3']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'gads_faq', 'a3', $defaults['gads_faq']['a3']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-5">
                    <div class="faq-question">
                        <h3><?= esc($content, 'gads_faq', 'q4', $defaults['gads_faq']['q4']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'gads_faq', 'a4', $defaults['gads_faq']['a4']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-6">
                    <div class="faq-question">
                        <h3><?= esc($content, 'gads_faq', 'q5', $defaults['gads_faq']['q5']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'gads_faq', 'a5', $defaults['gads_faq']['a5']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <div class="final-cta-content">
                <h2 class="animate-on-scroll"><?= esc($content, 'gads_cta', 'headline', $defaults['gads_cta']['headline']) ?></h2>
                <p class="animate-on-scroll stagger-1"><?= esc($content, 'gads_cta', 'subtitle', $defaults['gads_cta']['subtitle']) ?></p>
                <div class="cta-buttons animate-on-scroll stagger-2">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'gads_cta', 'cta_primary', $defaults['gads_cta']['cta_primary']) ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'gads_cta', 'cta_secondary', $defaults['gads_cta']['cta_secondary']) ?></a>
                </div>
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

<?php
require_once 'config/database.php';
$allDefaults = require 'config/services-defaults.php';
$defaults = $allDefaults['seo'];
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
    <title>SEO Services | Agile & Co</title>
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
                <p class="pre-headline"><?= esc($content, 'seo_hero', 'pre_headline', $defaults['seo_hero']['pre_headline']) ?></p>
                <h1><?= esc($content, 'seo_hero', 'headline', $defaults['seo_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'seo_hero', 'headline_accent', $defaults['seo_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'seo_hero', 'subtitle', $defaults['seo_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'seo_hero', 'cta_primary', $defaults['seo_hero']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, 'seo_hero', 'cta_secondary', $defaults['seo_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="core-intro section">
        <div class="container">
            <div class="core-intro-grid">
                <div class="core-intro-content animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'seo_core_intro', 'pre_headline', $defaults['seo_core_intro']['pre_headline']) ?></p>
                    <h2><?= esc($content, 'seo_core_intro', 'headline', $defaults['seo_core_intro']['headline']) ?> <span class="text-accent"><?= esc($content, 'seo_core_intro', 'headline_accent', $defaults['seo_core_intro']['headline_accent']) ?></span></h2>
                    <p><?= esc($content, 'seo_core_intro', 'paragraph1', $defaults['seo_core_intro']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'seo_core_intro', 'paragraph2', $defaults['seo_core_intro']['paragraph2']) ?></span></p>
                    <p><?= esc($content, 'seo_core_intro', 'paragraph3', $defaults['seo_core_intro']['paragraph3']) ?></p>
                    <p><?= sc($content, 'seo_core_intro', 'paragraph4', $defaults['seo_core_intro']['paragraph4']) ?></p>
                </div>
                <div class="core-intro-visual animate-on-scroll stagger-2">
                    <div class="core-badge">
                        <div class="core-badge-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <h3>Powered by Core</h3>
                        <p>Our proprietary AI engine</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="acronyms section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'seo_acronyms', 'pre_headline', $defaults['seo_acronyms']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'seo_acronyms', 'headline', $defaults['seo_acronyms']['headline']) ?> <span class="text-accent"><?= esc($content, 'seo_acronyms', 'headline_accent', $defaults['seo_acronyms']['headline_accent']) ?></span></h2>
            <p class="acronyms-intro animate-on-scroll stagger-2"><?= esc($content, 'seo_acronyms', 'intro', $defaults['seo_acronyms']['intro']) ?></p>
            <div class="acronyms-grid">
                <div class="acronym-card animate-on-scroll stagger-2">
                    <div class="acronym-abbr"><?= esc($content, 'seo_acronyms', 'card1_abbr', $defaults['seo_acronyms']['card1_abbr']) ?></div>
                    <div class="acronym-name"><?= esc($content, 'seo_acronyms', 'card1_name', $defaults['seo_acronyms']['card1_name']) ?></div>
                    <p><?= esc($content, 'seo_acronyms', 'card1_text', $defaults['seo_acronyms']['card1_text']) ?></p>
                </div>
                <div class="acronym-card animate-on-scroll stagger-3">
                    <div class="acronym-abbr"><?= esc($content, 'seo_acronyms', 'card2_abbr', $defaults['seo_acronyms']['card2_abbr']) ?></div>
                    <div class="acronym-name"><?= esc($content, 'seo_acronyms', 'card2_name', $defaults['seo_acronyms']['card2_name']) ?></div>
                    <p><?= esc($content, 'seo_acronyms', 'card2_text', $defaults['seo_acronyms']['card2_text']) ?></p>
                </div>
                <div class="acronym-card animate-on-scroll stagger-4">
                    <div class="acronym-abbr"><?= esc($content, 'seo_acronyms', 'card3_abbr', $defaults['seo_acronyms']['card3_abbr']) ?></div>
                    <div class="acronym-name"><?= esc($content, 'seo_acronyms', 'card3_name', $defaults['seo_acronyms']['card3_name']) ?></div>
                    <p><?= esc($content, 'seo_acronyms', 'card3_text', $defaults['seo_acronyms']['card3_text']) ?></p>
                </div>
            </div>
            <div class="acronyms-truth animate-on-scroll stagger-5">
                <h3><?= esc($content, 'seo_acronyms', 'truth_title', $defaults['seo_acronyms']['truth_title']) ?></h3>
                <p><?= sc($content, 'seo_acronyms', 'truth_text', $defaults['seo_acronyms']['truth_text']) ?></p>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="process-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'seo_process', 'pre_headline', $defaults['seo_process']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'seo_process', 'headline', $defaults['seo_process']['headline']) ?> <span class="text-accent"><?= esc($content, 'seo_process', 'headline_accent', $defaults['seo_process']['headline_accent']) ?></span></h2>
            <div class="process-timeline">
                <div class="process-item animate-on-scroll stagger-2">
                    <div class="process-number">1</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'seo_process', 'step1_title', $defaults['seo_process']['step1_title']) ?></h3>
                        <p><?= esc($content, 'seo_process', 'step1_text', $defaults['seo_process']['step1_text']) ?></p>
                        <div class="process-details">
                            <span class="process-tag">Technical Audit</span>
                            <span class="process-tag">SEO Audit</span>
                            <span class="process-tag">Competitor Analysis</span>
                            <span class="process-tag">Link Intersect</span>
                        </div>
                    </div>
                </div>
                <div class="process-item animate-on-scroll stagger-3">
                    <div class="process-number">2</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'seo_process', 'step2_title', $defaults['seo_process']['step2_title']) ?></h3>
                        <p><?= esc($content, 'seo_process', 'step2_text', $defaults['seo_process']['step2_text']) ?></p>
                    </div>
                </div>
                <div class="process-item animate-on-scroll stagger-4">
                    <div class="process-number">3</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'seo_process', 'step3_title', $defaults['seo_process']['step3_title']) ?></h3>
                        <p><?= esc($content, 'seo_process', 'step3_text', $defaults['seo_process']['step3_text']) ?></p>
                    </div>
                </div>
                <div class="process-item animate-on-scroll stagger-5">
                    <div class="process-number">4</div>
                    <div class="process-content">
                        <h3><?= esc($content, 'seo_process', 'step4_title', $defaults['seo_process']['step4_title']) ?></h3>
                        <p><?= esc($content, 'seo_process', 'step4_text', $defaults['seo_process']['step4_text']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="services-included section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'seo_services', 'pre_headline', $defaults['seo_services']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'seo_services', 'headline', $defaults['seo_services']['headline']) ?> <span class="text-accent"><?= esc($content, 'seo_services', 'headline_accent', $defaults['seo_services']['headline_accent']) ?></span></h2>
            <div class="services-grid">
                <div class="service-item animate-on-scroll stagger-1">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h3><?= esc($content, 'seo_services', 'card1_title', $defaults['seo_services']['card1_title']) ?></h3>
                    <p><?= esc($content, 'seo_services', 'card1_text', $defaults['seo_services']['card1_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-2">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3><?= esc($content, 'seo_services', 'card2_title', $defaults['seo_services']['card2_title']) ?></h3>
                    <p><?= esc($content, 'seo_services', 'card2_text', $defaults['seo_services']['card2_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-3">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <h3><?= esc($content, 'seo_services', 'card3_title', $defaults['seo_services']['card3_title']) ?></h3>
                    <p><?= esc($content, 'seo_services', 'card3_text', $defaults['seo_services']['card3_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-4">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h3><?= esc($content, 'seo_services', 'card4_title', $defaults['seo_services']['card4_title']) ?></h3>
                    <p><?= esc($content, 'seo_services', 'card4_text', $defaults['seo_services']['card4_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-5">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    </div>
                    <h3><?= esc($content, 'seo_services', 'card5_title', $defaults['seo_services']['card5_title']) ?></h3>
                    <p><?= esc($content, 'seo_services', 'card5_text', $defaults['seo_services']['card5_text']) ?></p>
                </div>
                <div class="service-item animate-on-scroll stagger-6">
                    <div class="service-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h3><?= esc($content, 'seo_services', 'card6_title', $defaults['seo_services']['card6_title']) ?></h3>
                    <p><?= esc($content, 'seo_services', 'card6_text', $defaults['seo_services']['card6_text']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="faq section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'seo_faq', 'pre_headline', $defaults['seo_faq']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'seo_faq', 'headline', $defaults['seo_faq']['headline']) ?> <span class="text-accent"><?= esc($content, 'seo_faq', 'headline_accent', $defaults['seo_faq']['headline_accent']) ?></span></h2>
            <div class="faq-list">
                <div class="faq-item animate-on-scroll stagger-2">
                    <div class="faq-question">
                        <h3><?= esc($content, 'seo_faq', 'q1', $defaults['seo_faq']['q1']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'seo_faq', 'a1', $defaults['seo_faq']['a1']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-3">
                    <div class="faq-question">
                        <h3><?= esc($content, 'seo_faq', 'q2', $defaults['seo_faq']['q2']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'seo_faq', 'a2', $defaults['seo_faq']['a2']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-4">
                    <div class="faq-question">
                        <h3><?= esc($content, 'seo_faq', 'q3', $defaults['seo_faq']['q3']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'seo_faq', 'a3', $defaults['seo_faq']['a3']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-5">
                    <div class="faq-question">
                        <h3><?= esc($content, 'seo_faq', 'q4', $defaults['seo_faq']['q4']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'seo_faq', 'a4', $defaults['seo_faq']['a4']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <div class="final-cta-content">
                <h2 class="animate-on-scroll"><?= esc($content, 'seo_cta', 'headline', $defaults['seo_cta']['headline']) ?></h2>
                <p class="animate-on-scroll stagger-1"><?= esc($content, 'seo_cta', 'subtitle', $defaults['seo_cta']['subtitle']) ?></p>
                <div class="cta-buttons animate-on-scroll stagger-2">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'seo_cta', 'cta_primary', $defaults['seo_cta']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                    <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'seo_cta', 'cta_secondary', $defaults['seo_cta']['cta_secondary']) ?></a>
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
            <div class="footer-bottom"><p>© 2025 Agile & Co. All rights reserved.</p><div class="footer-legal"><a href="#">Privacy Policy</a><a href="#">Terms of Service</a></div></div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/chatbot.js"></script>
</body>
</html>

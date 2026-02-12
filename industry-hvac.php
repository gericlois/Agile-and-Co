<?php
require_once 'config/database.php';
$allDefaults = require 'config/industries-defaults.php';
$defaults = $allDefaults['hvac'];
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
    <title>HVAC Marketing | Agile & Co</title>
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
                <div class="hero-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <?= esc($content, 'hvac_hero', 'badge', $defaults['hvac_hero']['badge']) ?>
                </div>
                <h1><?= esc($content, 'hvac_hero', 'headline', $defaults['hvac_hero']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_hero', 'headline_accent', $defaults['hvac_hero']['headline_accent']) ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'hvac_hero', 'subtitle', $defaults['hvac_hero']['subtitle']) ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'hvac_hero', 'cta_primary', $defaults['hvac_hero']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, 'hvac_hero', 'cta_secondary', $defaults['hvac_hero']['cta_secondary']) ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="intro-section section">
        <div class="container">
            <div class="intro-grid">
                <div class="intro-content animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, 'hvac_intro', 'pre_headline', $defaults['hvac_intro']['pre_headline']) ?></p>
                    <h2><?= esc($content, 'hvac_intro', 'headline', $defaults['hvac_intro']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_intro', 'headline_accent', $defaults['hvac_intro']['headline_accent']) ?></span></h2>
                    <p><?= esc($content, 'hvac_intro', 'paragraph1', $defaults['hvac_intro']['paragraph1']) ?></p>
                    <p><span class="highlight"><?= esc($content, 'hvac_intro', 'paragraph2', $defaults['hvac_intro']['paragraph2']) ?></span></p>
                    <p><?= esc($content, 'hvac_intro', 'paragraph3', $defaults['hvac_intro']['paragraph3']) ?></p>
                </div>
                <div class="intro-visual animate-on-scroll stagger-2">
                    <div class="intro-card">
                        <div class="intro-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3><?= esc($content, 'hvac_intro', 'card_title', $defaults['hvac_intro']['card_title']) ?></h3>
                        <p><?= esc($content, 'hvac_intro', 'card_text', $defaults['hvac_intro']['card_text']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="core-section section">
        <div class="container">
            <div class="core-header animate-on-scroll">
                <p class="pre-headline"><?= esc($content, 'hvac_core', 'pre_headline', $defaults['hvac_core']['pre_headline']) ?></p>
                <h2><?= esc($content, 'hvac_core', 'headline', $defaults['hvac_core']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_core', 'headline_accent', $defaults['hvac_core']['headline_accent']) ?></span></h2>
                <p><?= esc($content, 'hvac_core', 'intro_text', $defaults['hvac_core']['intro_text']) ?></p>
            </div>
            <div class="core-features">
                <div class="core-feature animate-on-scroll stagger-1">
                    <div class="core-feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    </div>
                    <div>
                        <h3><?= esc($content, 'hvac_core', 'feature1_title', $defaults['hvac_core']['feature1_title']) ?></h3>
                        <p><?= esc($content, 'hvac_core', 'feature1_text', $defaults['hvac_core']['feature1_text']) ?></p>
                    </div>
                </div>
                <div class="core-feature animate-on-scroll stagger-2">
                    <div class="core-feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <div>
                        <h3><?= esc($content, 'hvac_core', 'feature2_title', $defaults['hvac_core']['feature2_title']) ?></h3>
                        <p><?= esc($content, 'hvac_core', 'feature2_text', $defaults['hvac_core']['feature2_text']) ?></p>
                    </div>
                </div>
                <div class="core-feature animate-on-scroll stagger-3">
                    <div class="core-feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <h3><?= esc($content, 'hvac_core', 'feature3_title', $defaults['hvac_core']['feature3_title']) ?></h3>
                        <p><?= esc($content, 'hvac_core', 'feature3_text', $defaults['hvac_core']['feature3_text']) ?></p>
                    </div>
                </div>
                <div class="core-feature animate-on-scroll stagger-4">
                    <div class="core-feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                    <div>
                        <h3><?= esc($content, 'hvac_core', 'feature4_title', $defaults['hvac_core']['feature4_title']) ?></h3>
                        <p><?= esc($content, 'hvac_core', 'feature4_text', $defaults['hvac_core']['feature4_text']) ?></p>
                    </div>
                </div>
            </div>
            <div class="core-cta animate-on-scroll stagger-5">
                <a href="core.php" class="btn btn-secondary">Learn More About Core <span class="btn-arrow">→</span></a>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="challenges section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'hvac_challenges', 'pre_headline', $defaults['hvac_challenges']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'hvac_challenges', 'headline', $defaults['hvac_challenges']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_challenges', 'headline_accent', $defaults['hvac_challenges']['headline_accent']) ?></span></h2>
            <div class="challenges-grid">
                <div class="challenge-card animate-on-scroll stagger-2">
                    <div class="challenge-number">01</div>
                    <h3><?= esc($content, 'hvac_challenges', 'card1_title', $defaults['hvac_challenges']['card1_title']) ?></h3>
                    <p><?= esc($content, 'hvac_challenges', 'card1_text', $defaults['hvac_challenges']['card1_text']) ?></p>
                    <div class="challenge-solution">
                        <strong>How We Help</strong>
                        <p><?= esc($content, 'hvac_challenges', 'card1_solution', $defaults['hvac_challenges']['card1_solution']) ?></p>
                    </div>
                </div>
                <div class="challenge-card animate-on-scroll stagger-3">
                    <div class="challenge-number">02</div>
                    <h3><?= esc($content, 'hvac_challenges', 'card2_title', $defaults['hvac_challenges']['card2_title']) ?></h3>
                    <p><?= esc($content, 'hvac_challenges', 'card2_text', $defaults['hvac_challenges']['card2_text']) ?></p>
                    <div class="challenge-solution">
                        <strong>How We Help</strong>
                        <p><?= esc($content, 'hvac_challenges', 'card2_solution', $defaults['hvac_challenges']['card2_solution']) ?></p>
                    </div>
                </div>
                <div class="challenge-card animate-on-scroll stagger-4">
                    <div class="challenge-number">03</div>
                    <h3><?= esc($content, 'hvac_challenges', 'card3_title', $defaults['hvac_challenges']['card3_title']) ?></h3>
                    <p><?= esc($content, 'hvac_challenges', 'card3_text', $defaults['hvac_challenges']['card3_text']) ?></p>
                    <div class="challenge-solution">
                        <strong>How We Help</strong>
                        <p><?= esc($content, 'hvac_challenges', 'card3_solution', $defaults['hvac_challenges']['card3_solution']) ?></p>
                    </div>
                </div>
                <div class="challenge-card animate-on-scroll stagger-5">
                    <div class="challenge-number">04</div>
                    <h3><?= esc($content, 'hvac_challenges', 'card4_title', $defaults['hvac_challenges']['card4_title']) ?></h3>
                    <p><?= esc($content, 'hvac_challenges', 'card4_text', $defaults['hvac_challenges']['card4_text']) ?></p>
                    <div class="challenge-solution">
                        <strong>How We Help</strong>
                        <p><?= esc($content, 'hvac_challenges', 'card4_solution', $defaults['hvac_challenges']['card4_solution']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="services-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'hvac_services', 'pre_headline', $defaults['hvac_services']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'hvac_services', 'headline', $defaults['hvac_services']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_services', 'headline_accent', $defaults['hvac_services']['headline_accent']) ?></span></h2>
            <p class="services-intro animate-on-scroll stagger-2"><?= esc($content, 'hvac_services', 'intro', $defaults['hvac_services']['intro']) ?></p>
            <div class="services-grid">
                <div class="service-card animate-on-scroll stagger-2">
                    <div class="service-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_services', 'card1_title', $defaults['hvac_services']['card1_title']) ?></h3>
                    <p><?= esc($content, 'hvac_services', 'card1_text', $defaults['hvac_services']['card1_text']) ?></p>
                    <a href="services-seo.php" class="service-card-link">Learn More <span>→</span></a>
                </div>
                <div class="service-card animate-on-scroll stagger-3">
                    <div class="service-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_services', 'card2_title', $defaults['hvac_services']['card2_title']) ?></h3>
                    <p><?= esc($content, 'hvac_services', 'card2_text', $defaults['hvac_services']['card2_text']) ?></p>
                    <a href="services-google-ads.php" class="service-card-link">Learn More <span>→</span></a>
                </div>
                <div class="service-card animate-on-scroll stagger-4">
                    <div class="service-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_services', 'card3_title', $defaults['hvac_services']['card3_title']) ?></h3>
                    <p><?= esc($content, 'hvac_services', 'card3_text', $defaults['hvac_services']['card3_text']) ?></p>
                    <a href="services-meta-ads.php" class="service-card-link">Learn More <span>→</span></a>
                </div>
                <div class="service-card animate-on-scroll stagger-5">
                    <div class="service-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_services', 'card4_title', $defaults['hvac_services']['card4_title']) ?></h3>
                    <p><?= esc($content, 'hvac_services', 'card4_text', $defaults['hvac_services']['card4_text']) ?></p>
                    <a href="services-web-design.php" class="service-card-link">Learn More <span>→</span></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="timeline-section section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'hvac_timeline', 'pre_headline', $defaults['hvac_timeline']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'hvac_timeline', 'headline', $defaults['hvac_timeline']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_timeline', 'headline_accent', $defaults['hvac_timeline']['headline_accent']) ?></span></h2>
            <div class="timeline">
                <div class="timeline-item animate-on-scroll stagger-2">
                    <span class="timeline-period"><?= esc($content, 'hvac_timeline', 'step1_period', $defaults['hvac_timeline']['step1_period']) ?></span>
                    <h3><?= esc($content, 'hvac_timeline', 'step1_title', $defaults['hvac_timeline']['step1_title']) ?></h3>
                    <p><?= esc($content, 'hvac_timeline', 'step1_text', $defaults['hvac_timeline']['step1_text']) ?></p>
                </div>
                <div class="timeline-item animate-on-scroll stagger-3">
                    <span class="timeline-period"><?= esc($content, 'hvac_timeline', 'step2_period', $defaults['hvac_timeline']['step2_period']) ?></span>
                    <h3><?= esc($content, 'hvac_timeline', 'step2_title', $defaults['hvac_timeline']['step2_title']) ?></h3>
                    <p><?= esc($content, 'hvac_timeline', 'step2_text', $defaults['hvac_timeline']['step2_text']) ?></p>
                </div>
                <div class="timeline-item animate-on-scroll stagger-4">
                    <span class="timeline-period"><?= esc($content, 'hvac_timeline', 'step3_period', $defaults['hvac_timeline']['step3_period']) ?></span>
                    <h3><?= esc($content, 'hvac_timeline', 'step3_title', $defaults['hvac_timeline']['step3_title']) ?></h3>
                    <p><?= esc($content, 'hvac_timeline', 'step3_text', $defaults['hvac_timeline']['step3_text']) ?></p>
                </div>
                <div class="timeline-item animate-on-scroll stagger-5">
                    <span class="timeline-period"><?= esc($content, 'hvac_timeline', 'step4_period', $defaults['hvac_timeline']['step4_period']) ?></span>
                    <h3><?= esc($content, 'hvac_timeline', 'step4_title', $defaults['hvac_timeline']['step4_title']) ?></h3>
                    <p><?= esc($content, 'hvac_timeline', 'step4_text', $defaults['hvac_timeline']['step4_text']) ?></p>
                </div>
                <div class="timeline-item animate-on-scroll stagger-6">
                    <span class="timeline-period"><?= esc($content, 'hvac_timeline', 'step5_period', $defaults['hvac_timeline']['step5_period']) ?></span>
                    <h3><?= esc($content, 'hvac_timeline', 'step5_title', $defaults['hvac_timeline']['step5_title']) ?></h3>
                    <p><?= esc($content, 'hvac_timeline', 'step5_text', $defaults['hvac_timeline']['step5_text']) ?></p>
                </div>
            </div>
            <div class="timeline-highlight animate-on-scroll">
                <p><span class="highlight">The bottom line:</span> <?= esc($content, 'hvac_timeline', 'bottom_text', $defaults['hvac_timeline']['bottom_text']) ?></p>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="why-choose section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'hvac_why', 'pre_headline', $defaults['hvac_why']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'hvac_why', 'headline', $defaults['hvac_why']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_why', 'headline_accent', $defaults['hvac_why']['headline_accent']) ?></span></h2>
            <div class="why-grid">
                <div class="why-card animate-on-scroll stagger-2">
                    <div class="why-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_why', 'card1_title', $defaults['hvac_why']['card1_title']) ?></h3>
                    <p><?= esc($content, 'hvac_why', 'card1_text', $defaults['hvac_why']['card1_text']) ?></p>
                </div>
                <div class="why-card animate-on-scroll stagger-3">
                    <div class="why-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_why', 'card2_title', $defaults['hvac_why']['card2_title']) ?></h3>
                    <p><?= esc($content, 'hvac_why', 'card2_text', $defaults['hvac_why']['card2_text']) ?></p>
                </div>
                <div class="why-card animate-on-scroll stagger-4">
                    <div class="why-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_why', 'card3_title', $defaults['hvac_why']['card3_title']) ?></h3>
                    <p><?= esc($content, 'hvac_why', 'card3_text', $defaults['hvac_why']['card3_text']) ?></p>
                </div>
                <div class="why-card animate-on-scroll stagger-5">
                    <div class="why-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h3><?= esc($content, 'hvac_why', 'card4_title', $defaults['hvac_why']['card4_title']) ?></h3>
                    <p><?= esc($content, 'hvac_why', 'card4_text', $defaults['hvac_why']['card4_text']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="faq section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'hvac_faq', 'pre_headline', $defaults['hvac_faq']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'hvac_faq', 'headline', $defaults['hvac_faq']['headline']) ?> <span class="text-accent"><?= esc($content, 'hvac_faq', 'headline_accent', $defaults['hvac_faq']['headline_accent']) ?></span></h2>
            <div class="faq-list">
                <div class="faq-item animate-on-scroll stagger-2">
                    <div class="faq-question">
                        <h3><?= esc($content, 'hvac_faq', 'q1', $defaults['hvac_faq']['q1']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'hvac_faq', 'a1', $defaults['hvac_faq']['a1']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-3">
                    <div class="faq-question">
                        <h3><?= esc($content, 'hvac_faq', 'q2', $defaults['hvac_faq']['q2']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'hvac_faq', 'a2', $defaults['hvac_faq']['a2']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-4">
                    <div class="faq-question">
                        <h3><?= esc($content, 'hvac_faq', 'q3', $defaults['hvac_faq']['q3']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'hvac_faq', 'a3', $defaults['hvac_faq']['a3']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-5">
                    <div class="faq-question">
                        <h3><?= esc($content, 'hvac_faq', 'q4', $defaults['hvac_faq']['q4']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'hvac_faq', 'a4', $defaults['hvac_faq']['a4']) ?></p>
                    </div>
                </div>
                <div class="faq-item animate-on-scroll stagger-6">
                    <div class="faq-question">
                        <h3><?= esc($content, 'hvac_faq', 'q5', $defaults['hvac_faq']['q5']) ?></h3>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div class="faq-answer">
                        <p><?= esc($content, 'hvac_faq', 'a5', $defaults['hvac_faq']['a5']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <div class="final-cta-content">
                <h2 class="animate-on-scroll"><?= esc($content, 'hvac_cta', 'headline', $defaults['hvac_cta']['headline']) ?></h2>
                <p class="animate-on-scroll stagger-1"><?= esc($content, 'hvac_cta', 'subtitle', $defaults['hvac_cta']['subtitle']) ?></p>
                <div class="cta-buttons animate-on-scroll stagger-2">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'hvac_cta', 'cta_primary', $defaults['hvac_cta']['cta_primary']) ?> <span class="btn-arrow">→</span></a>
                    <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'hvac_cta', 'cta_secondary', $defaults['hvac_cta']['cta_secondary']) ?></a>
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

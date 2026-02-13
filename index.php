<?php
require_once 'config/database.php';
$defaults = require 'config/homepage-defaults.php';

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
    <title>Agile & Co | AI-Powered Marketing for Service Businesses</title>
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
                        <li><a href="contact.php">Industries</a></li>
                        <li><a href="contact.php">Core</a></li>
                        <li><a href="contact.php">About</a></li>
                    </ul>
                    <a href="contact.php" class="btn btn-primary">Let's Talk Growth</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="container">
            <div class="hero-inner">
                <div class="hero-content">
                    <h1><span><span class="accent-word"><?= esc($content, 'hero', 'headline_line1', $defaults['hero']['headline_line1']) ?></span></span><span><?= esc($content, 'hero', 'headline_line2', $defaults['hero']['headline_line2']) ?></span><span><?= esc($content, 'hero', 'headline_line3', $defaults['hero']['headline_line3']) ?></span></h1>
                    <p class="hero-subtitle"><?= esc($content, 'hero', 'subtitle', $defaults['hero']['subtitle']) ?></p>
                    <div class="hero-ctas">
                        <a href="contact.php" class="btn btn-primary"><?= esc($content, 'hero', 'cta_primary_text', $defaults['hero']['cta_primary_text']) ?> <span class="btn-arrow">→</span></a>
                        <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'hero', 'cta_secondary_text', $defaults['hero']['cta_secondary_text']) ?></a>
                    </div>
                </div>
                <div class="hero-graphic">
                    <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="hero-svg">
                        <circle cx="200" cy="200" r="180" stroke="#4CC9F0" stroke-width="1" opacity="0.2" class="ring ring-1"/>
                        <circle cx="200" cy="200" r="150" stroke="#4CC9F0" stroke-width="1" opacity="0.3" class="ring ring-2"/>
                        <circle cx="200" cy="200" r="120" stroke="#4CC9F0" stroke-width="1.5" opacity="0.4" class="ring ring-3"/>
                        <circle cx="200" cy="200" r="90" stroke="#4CC9F0" stroke-width="2" opacity="0.6" class="ring ring-4"/>
                        <circle cx="200" cy="200" r="55" fill="url(#coreGradient)" class="core-circle"/>
                        <circle cx="200" cy="200" r="45" fill="#0d0d0d"/>
                        <text x="200" y="210" text-anchor="middle" font-family="Space Grotesk, sans-serif" font-size="24" font-weight="700" fill="#4CC9F0" opacity="0.4" class="core-text">CORE</text>
                        <circle cx="200" cy="20" r="6" fill="#4CC9F0" class="orbit-dot orbit-1"/>
                        <circle cx="380" cy="200" r="5" fill="#4CC9F0" opacity="0.8" class="orbit-dot orbit-2"/>
                        <circle cx="200" cy="380" r="4" fill="#4CC9F0" opacity="0.6" class="orbit-dot orbit-3"/>
                        <circle cx="50" cy="200" r="5" fill="#4CC9F0" opacity="0.7" class="orbit-dot orbit-4"/>
                        <path d="M200 70 L200 145" stroke="#4CC9F0" stroke-width="2" opacity="0.5" class="connect-line"/>
                        <path d="M255 200 L330 200" stroke="#4CC9F0" stroke-width="2" opacity="0.5" class="connect-line"/>
                        <path d="M200 255 L200 330" stroke="#4CC9F0" stroke-width="2" opacity="0.5" class="connect-line"/>
                        <path d="M145 200 L70 200" stroke="#4CC9F0" stroke-width="2" opacity="0.5" class="connect-line"/>
                        <circle cx="290" cy="110" r="8" fill="#4CC9F0" opacity="0.6"/>
                        <circle cx="310" cy="290" r="6" fill="#4CC9F0" opacity="0.5"/>
                        <circle cx="90" cy="310" r="7" fill="#4CC9F0" opacity="0.5"/>
                        <circle cx="110" cy="90" r="5" fill="#4CC9F0" opacity="0.4"/>
                        <defs>
                            <radialGradient id="coreGradient" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#4CC9F0" stop-opacity="0.8"/>
                                <stop offset="100%" stop-color="#3BA8CC" stop-opacity="0.3"/>
                            </radialGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <section class="social-proof">
        <div class="container">
            <div class="social-proof-inner">
                <div class="stat-item">
                    <span class="stat-number"><?= esc($content, 'social_proof', 'stat1_number', $defaults['social_proof']['stat1_number']) ?></span>
                    <span class="stat-label"><?= esc($content, 'social_proof', 'stat1_label', $defaults['social_proof']['stat1_label']) ?></span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number"><?= esc($content, 'social_proof', 'stat2_number', $defaults['social_proof']['stat2_number']) ?></span>
                    <span class="stat-label"><?= esc($content, 'social_proof', 'stat2_label', $defaults['social_proof']['stat2_label']) ?></span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number"><?= esc($content, 'social_proof', 'stat3_number', $defaults['social_proof']['stat3_number']) ?></span>
                    <span class="stat-label"><?= esc($content, 'social_proof', 'stat3_label', $defaults['social_proof']['stat3_label']) ?></span>
                </div>
            </div>
        </div>
    </section>

    <section class="problem section">
        <div class="container">
            <div class="problem-content">
                <p class="pre-headline animate-on-scroll"><?= esc($content, 'problem', 'pre_headline', $defaults['problem']['pre_headline']) ?></p>
                <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'problem', 'headline', $defaults['problem']['headline']) ?></h2>
                <p class="problem-text animate-on-scroll stagger-2"><?= sc($content, 'problem', 'paragraph1', $defaults['problem']['paragraph1']) ?></p>
                <p class="problem-text animate-on-scroll stagger-3"><?= sc($content, 'problem', 'paragraph2', $defaults['problem']['paragraph2']) ?></p>
                <p class="problem-text animate-on-scroll stagger-4"><?= sc($content, 'problem', 'paragraph3', $defaults['problem']['paragraph3']) ?></p>
                <p class="problem-text animate-on-scroll stagger-5"><?= sc($content, 'problem', 'paragraph4', $defaults['problem']['paragraph4']) ?></p>
                <p class="problem-highlight animate-on-scroll stagger-6"><?= esc($content, 'problem', 'highlight', $defaults['problem']['highlight']) ?></p>
            </div>
        </div>
    </section>

    <section class="core section">
        <div class="core-bg"></div>
        <div class="container">
            <div class="core-header">
                <p class="pre-headline animate-on-scroll"><?= esc($content, 'core', 'pre_headline', $defaults['core']['pre_headline']) ?></p>
                <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'core', 'headline', $defaults['core']['headline']) ?> <span class="text-accent"><?= esc($content, 'core', 'headline_accent', $defaults['core']['headline_accent']) ?></span></h2>
                <p class="core-subtitle animate-on-scroll stagger-2"><?= esc($content, 'core', 'subtitle', $defaults['core']['subtitle']) ?></p>
            </div>
            <div class="core-cards">
                <div class="core-card animate-on-scroll scale stagger-1">
                    <div class="core-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" /></svg></div>
                    <h3><?= esc($content, 'core', 'card1_title', $defaults['core']['card1_title']) ?></h3>
                    <p><?= esc($content, 'core', 'card1_text', $defaults['core']['card1_text']) ?></p>
                    <p class="core-card-translation"><?= esc($content, 'core', 'card1_translation', $defaults['core']['card1_translation']) ?></p>
                </div>
                <div class="core-card animate-on-scroll scale stagger-2">
                    <div class="core-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg></div>
                    <h3><?= esc($content, 'core', 'card2_title', $defaults['core']['card2_title']) ?></h3>
                    <p><?= esc($content, 'core', 'card2_text', $defaults['core']['card2_text']) ?></p>
                    <p class="core-card-translation"><?= esc($content, 'core', 'card2_translation', $defaults['core']['card2_translation']) ?></p>
                </div>
                <div class="core-card animate-on-scroll scale stagger-3">
                    <div class="core-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
                    <h3><?= esc($content, 'core', 'card3_title', $defaults['core']['card3_title']) ?></h3>
                    <p><?= esc($content, 'core', 'card3_text', $defaults['core']['card3_text']) ?></p>
                    <p class="core-card-translation"><?= esc($content, 'core', 'card3_translation', $defaults['core']['card3_translation']) ?></p>
                </div>
            </div>
            <div class="core-cta animate-on-scroll stagger-4"><a href="contact.php" class="btn btn-primary"><?= esc($content, 'core', 'cta_text', $defaults['core']['cta_text']) ?> <span class="btn-arrow">→</span></a></div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="services section">
        <div class="container">
            <div class="services-header">
                <p class="pre-headline animate-on-scroll"><?= esc($content, 'services', 'pre_headline', $defaults['services']['pre_headline']) ?></p>
                <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'services', 'headline', $defaults['services']['headline']) ?> <span class="text-accent"><?= esc($content, 'services', 'headline_accent', $defaults['services']['headline_accent']) ?></span></h2>
                <p class="services-subtitle animate-on-scroll stagger-2"><?= esc($content, 'services', 'subtitle', $defaults['services']['subtitle']) ?></p>
            </div>
            <div class="services-grid">
                <div class="service-card animate-on-scroll from-left stagger-1">
                    <div class="service-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></div>
                    <h3><?= esc($content, 'services', 'card1_title', $defaults['services']['card1_title']) ?></h3>
                    <p><?= esc($content, 'services', 'card1_text', $defaults['services']['card1_text']) ?></p>
                    <a href="contact.php" class="service-link">Learn More <span>→</span></a>
                </div>
                <div class="service-card animate-on-scroll from-right stagger-2">
                    <div class="service-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg></div>
                    <h3><?= esc($content, 'services', 'card2_title', $defaults['services']['card2_title']) ?></h3>
                    <p><?= esc($content, 'services', 'card2_text', $defaults['services']['card2_text']) ?></p>
                    <a href="contact.php" class="service-link">Learn More <span>→</span></a>
                </div>
                <div class="service-card animate-on-scroll from-left stagger-3">
                    <div class="service-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg></div>
                    <h3><?= esc($content, 'services', 'card3_title', $defaults['services']['card3_title']) ?></h3>
                    <p><?= esc($content, 'services', 'card3_text', $defaults['services']['card3_text']) ?></p>
                    <a href="contact.php" class="service-link">Learn More <span>→</span></a>
                </div>
                <div class="service-card animate-on-scroll from-right stagger-4">
                    <div class="service-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
                    <h3><?= esc($content, 'services', 'card4_title', $defaults['services']['card4_title']) ?></h3>
                    <p><?= esc($content, 'services', 'card4_text', $defaults['services']['card4_text']) ?></p>
                    <a href="contact.php" class="service-link">Learn More <span>→</span></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="differentiators section">
        <div class="container">
            <div class="differentiators-header">
                <p class="pre-headline animate-on-scroll"><?= esc($content, 'differentiators', 'pre_headline', $defaults['differentiators']['pre_headline']) ?></p>
                <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'differentiators', 'headline', $defaults['differentiators']['headline']) ?> <span class="text-accent"><?= esc($content, 'differentiators', 'headline_accent', $defaults['differentiators']['headline_accent']) ?></span></h2>
                <p class="differentiators-subtitle animate-on-scroll stagger-2"><?= esc($content, 'differentiators', 'subtitle', $defaults['differentiators']['subtitle']) ?></p>
            </div>
            <div class="diff-list">
                <div class="diff-item animate-on-scroll">
                    <div class="diff-number">01</div>
                    <div class="diff-content">
                        <h3><?= esc($content, 'differentiators', 'item1_title', $defaults['differentiators']['item1_title']) ?></h3>
                        <p><?= esc($content, 'differentiators', 'item1_text', $defaults['differentiators']['item1_text']) ?></p>
                    </div>
                    <div class="diff-visual">
                        <div class="diff-graphic">
                            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="100" cy="100" r="80" stroke="#4CC9F0" stroke-width="1" opacity="0.3"/>
                                <circle cx="100" cy="100" r="60" stroke="#4CC9F0" stroke-width="1" opacity="0.5"/>
                                <circle cx="100" cy="100" r="40" stroke="#4CC9F0" stroke-width="2" opacity="0.8"/>
                                <circle cx="100" cy="100" r="8" fill="#4CC9F0"/>
                                <path d="M100 20 L100 40" stroke="#4CC9F0" stroke-width="2"/>
                                <path d="M100 160 L100 180" stroke="#4CC9F0" stroke-width="2"/>
                                <path d="M20 100 L40 100" stroke="#4CC9F0" stroke-width="2"/>
                                <path d="M160 100 L180 100" stroke="#4CC9F0" stroke-width="2"/>
                                <circle cx="100" cy="40" r="4" fill="#4CC9F0"/>
                                <circle cx="100" cy="160" r="4" fill="#4CC9F0"/>
                                <circle cx="40" cy="100" r="4" fill="#4CC9F0"/>
                                <circle cx="160" cy="100" r="4" fill="#4CC9F0"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="diff-item animate-on-scroll stagger-1">
                    <div class="diff-number">02</div>
                    <div class="diff-content">
                        <h3><?= esc($content, 'differentiators', 'item2_title', $defaults['differentiators']['item2_title']) ?></h3>
                        <p><?= esc($content, 'differentiators', 'item2_text', $defaults['differentiators']['item2_text']) ?></p>
                    </div>
                    <div class="diff-visual">
                        <div class="diff-graphic">
                            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="40" y="60" width="120" height="80" rx="8" stroke="#4CC9F0" stroke-width="2" opacity="0.8"/>
                                <path d="M40 85 L160 85" stroke="#4CC9F0" stroke-width="1" opacity="0.5"/>
                                <circle cx="60" cy="72" r="4" fill="#4CC9F0" opacity="0.6"/>
                                <circle cx="75" cy="72" r="4" fill="#4CC9F0" opacity="0.6"/>
                                <circle cx="90" cy="72" r="4" fill="#4CC9F0" opacity="0.6"/>
                                <path d="M55 105 L90 105" stroke="#4CC9F0" stroke-width="3" stroke-linecap="round"/>
                                <path d="M55 120 L120 120" stroke="#4CC9F0" stroke-width="3" stroke-linecap="round" opacity="0.5"/>
                                <path d="M130 100 L145 115 L175 75" stroke="#4CC9F0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="diff-item animate-on-scroll stagger-2">
                    <div class="diff-number">03</div>
                    <div class="diff-content">
                        <h3><?= esc($content, 'differentiators', 'item3_title', $defaults['differentiators']['item3_title']) ?></h3>
                        <p><?= esc($content, 'differentiators', 'item3_text', $defaults['differentiators']['item3_text']) ?></p>
                    </div>
                    <div class="diff-visual">
                        <div class="diff-graphic">
                            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M30 170 L30 50" stroke="#4CC9F0" stroke-width="2" opacity="0.5"/>
                                <path d="M30 170 L180 170" stroke="#4CC9F0" stroke-width="2" opacity="0.5"/>
                                <path d="M50 140 L80 110 L110 125 L140 70 L170 50" stroke="#4CC9F0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="50" cy="140" r="5" fill="#4CC9F0"/>
                                <circle cx="80" cy="110" r="5" fill="#4CC9F0"/>
                                <circle cx="110" cy="125" r="5" fill="#4CC9F0"/>
                                <circle cx="140" cy="70" r="5" fill="#4CC9F0"/>
                                <circle cx="170" cy="50" r="8" fill="#4CC9F0"/>
                                <path d="M50 140 L50 170" stroke="#4CC9F0" stroke-width="1" opacity="0.3" stroke-dasharray="4 4"/>
                                <path d="M170 50 L170 170" stroke="#4CC9F0" stroke-width="1" opacity="0.3" stroke-dasharray="4 4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="diff-item animate-on-scroll stagger-3">
                    <div class="diff-number">04</div>
                    <div class="diff-content">
                        <h3><?= esc($content, 'differentiators', 'item4_title', $defaults['differentiators']['item4_title']) ?></h3>
                        <p><?= esc($content, 'differentiators', 'item4_text', $defaults['differentiators']['item4_text']) ?></p>
                    </div>
                    <div class="diff-visual">
                        <div class="diff-graphic">
                            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="100" cy="80" r="30" stroke="#4CC9F0" stroke-width="2"/>
                                <circle cx="100" cy="80" r="12" fill="#4CC9F0" opacity="0.8"/>
                                <path d="M100 110 L100 140" stroke="#4CC9F0" stroke-width="2"/>
                                <path d="M70 180 L100 140 L130 180" stroke="#4CC9F0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="50" cy="130" r="15" stroke="#4CC9F0" stroke-width="1" opacity="0.5"/>
                                <circle cx="150" cy="130" r="15" stroke="#4CC9F0" stroke-width="1" opacity="0.5"/>
                                <circle cx="50" cy="130" r="6" fill="#4CC9F0" opacity="0.5"/>
                                <circle cx="150" cy="130" r="6" fill="#4CC9F0" opacity="0.5"/>
                                <path d="M65 130 L85 100" stroke="#4CC9F0" stroke-width="1" opacity="0.4"/>
                                <path d="M135 130 L115 100" stroke="#4CC9F0" stroke-width="1" opacity="0.4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials section">
        <div class="testimonials-bg"></div>
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'testimonials', 'headline', $defaults['testimonials']['headline']) ?></h2>
            <div class="testimonials-grid">
                <div class="testimonial-card animate-on-scroll stagger-1"><div class="quote-mark">"</div><p class="testimonial-quote"><?= esc($content, 'testimonials', 'quote1_text', $defaults['testimonials']['quote1_text']) ?></p><p class="testimonial-author"><?= esc($content, 'testimonials', 'quote1_author', $defaults['testimonials']['quote1_author']) ?></p></div>
                <div class="testimonial-card animate-on-scroll stagger-2"><div class="quote-mark">"</div><p class="testimonial-quote"><?= esc($content, 'testimonials', 'quote2_text', $defaults['testimonials']['quote2_text']) ?></p><p class="testimonial-author"><?= esc($content, 'testimonials', 'quote2_author', $defaults['testimonials']['quote2_author']) ?></p></div>
                <div class="testimonial-card animate-on-scroll stagger-3"><div class="quote-mark">"</div><p class="testimonial-quote"><?= esc($content, 'testimonials', 'quote3_text', $defaults['testimonials']['quote3_text']) ?></p><p class="testimonial-author"><?= esc($content, 'testimonials', 'quote3_author', $defaults['testimonials']['quote3_author']) ?></p></div>
                <div class="testimonial-card animate-on-scroll stagger-4"><div class="quote-mark">"</div><p class="testimonial-quote"><?= esc($content, 'testimonials', 'quote4_text', $defaults['testimonials']['quote4_text']) ?></p><p class="testimonial-author"><?= esc($content, 'testimonials', 'quote4_author', $defaults['testimonials']['quote4_author']) ?></p></div>
            </div>
        </div>
    </section>

    <section class="process section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, 'process', 'pre_headline', $defaults['process']['pre_headline']) ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, 'process', 'headline', $defaults['process']['headline']) ?> <span class="text-accent"><?= esc($content, 'process', 'headline_accent', $defaults['process']['headline_accent']) ?></span></h2>
            <div class="process-steps">
                <div class="process-step animate-on-scroll stagger-2"><div class="step-number">1</div><h3><?= esc($content, 'process', 'step1_title', $defaults['process']['step1_title']) ?></h3><p><?= esc($content, 'process', 'step1_text', $defaults['process']['step1_text']) ?></p></div>
                <div class="process-step animate-on-scroll stagger-3"><div class="step-number">2</div><h3><?= esc($content, 'process', 'step2_title', $defaults['process']['step2_title']) ?></h3><p><?= esc($content, 'process', 'step2_text', $defaults['process']['step2_text']) ?></p></div>
                <div class="process-step animate-on-scroll stagger-4"><div class="step-number">3</div><h3><?= esc($content, 'process', 'step3_title', $defaults['process']['step3_title']) ?></h3><p><?= esc($content, 'process', 'step3_text', $defaults['process']['step3_text']) ?></p></div>
            </div>
            <div class="process-cta animate-on-scroll stagger-5"><a href="contact.php" class="btn btn-primary"><?= esc($content, 'process', 'cta_text', $defaults['process']['cta_text']) ?> <span class="btn-arrow">→</span></a></div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <div class="final-cta-content">
                <h2 class="animate-on-scroll"><?= esc($content, 'final_cta', 'headline', $defaults['final_cta']['headline']) ?></h2>
                <p class="animate-on-scroll stagger-1"><?= esc($content, 'final_cta', 'subtitle', $defaults['final_cta']['subtitle']) ?></p>
                <a href="contact.php" class="btn btn-primary animate-on-scroll stagger-2"><?= esc($content, 'final_cta', 'cta_text', $defaults['final_cta']['cta_text']) ?> <span class="btn-arrow">→</span></a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand"><a href="index.php" class="logo" id="footer-logo">Agile & Co</a><p>AI-powered marketing for local service businesses. More leads. More booked jobs. Zero guesswork.</p></div>
                <div class="footer-col"><h4>Services</h4><ul><li><a href="contact.php">SEO</a></li><li><a href="contact.php">Google Ads</a></li><li><a href="contact.php">Meta Ads</a></li><li><a href="contact.php">Website Design</a></li></ul></div>
                <div class="footer-col"><h4>Industries</h4><ul><li><a href="contact.php">Home Services</a></li><li><a href="contact.php">Healthcare</a></li><li><a href="contact.php">Automotive</a></li><li><a href="contact.php">View All</a></li></ul></div>
                <div class="footer-col"><h4>Company</h4><ul><li><a href="contact.php">About Us</a></li><li><a href="contact.php">Core</a></li><li><a href="contact.php">Blog</a></li><li><a href="contact.php">Contact</a></li><li><a href="quiz.php">Free Quiz</a></li></ul></div>
            </div>
            <div class="footer-bottom"><p>© 2025 Agile & Co. All rights reserved.</p><div class="footer-legal"><a href="contact.php">Privacy Policy</a><a href="contact.php">Terms of Service</a></div></div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/chatbot.js"></script>
    <script>
    (function(){var c=0,t;document.getElementById('footer-logo').addEventListener('click',function(e){e.preventDefault();c++;clearTimeout(t);t=setTimeout(function(){c=0},600);if(c===3){window.location.href='admin/login.php';}});})();
    </script>
</body>
</html>

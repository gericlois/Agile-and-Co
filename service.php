<?php
require_once 'config/database.php';

$slug = trim($_GET['s'] ?? '');

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

// If slug provided, load the specific service
$service = null;
if ($slug) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    $service = $stmt->fetch();
    if (!$service) {
        header('Location: service.php');
        exit;
    }
}

// Load all active services for listing page (and footer)
$allServices = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll();

if ($service):
// ===================== INDIVIDUAL SERVICE PAGE =====================
$s = $slug;
$label = htmlspecialchars($service['label']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $label ?> Services | Agile & Co</title>
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
                <p class="pre-headline"><?= esc($content, $s.'_hero', 'pre_headline', $label . ' Services') ?></p>
                <h1><?= esc($content, $s.'_hero', 'headline', $label) ?> <span class="text-accent"><?= esc($content, $s.'_hero', 'headline_accent', 'Powered by AI.') ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, $s.'_hero', 'subtitle', 'AI-powered ' . strtolower($service['label']) . ' services for local service businesses.') ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, $s.'_hero', 'cta_primary', 'Get Started') ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, $s.'_hero', 'cta_secondary', 'Learn About Core') ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="content-section section">
        <div class="container">
            <div class="content-grid">
                <div class="content-text animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, $s.'_intro', 'pre_headline', 'How We Help') ?></p>
                    <h2><?= esc($content, $s.'_intro', 'headline', 'Our Approach to') ?> <span class="text-accent"><?= esc($content, $s.'_intro', 'headline_accent', $label . '.') ?></span></h2>
                    <p><?= esc($content, $s.'_intro', 'paragraph1', '') ?></p>
                    <?php if (sc($content, $s.'_intro', 'paragraph2')): ?>
                        <p><span class="highlight"><?= esc($content, $s.'_intro', 'paragraph2') ?></span></p>
                    <?php endif; ?>
                    <?php if (sc($content, $s.'_intro', 'paragraph3')): ?>
                        <p><?= esc($content, $s.'_intro', 'paragraph3') ?></p>
                    <?php endif; ?>
                    <?php if (sc($content, $s.'_intro', 'paragraph4')): ?>
                        <p><?= sc($content, $s.'_intro', 'paragraph4') ?></p>
                    <?php endif; ?>
                </div>
                <div class="animate-on-scroll stagger-2">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <h3>Powered by Core</h3>
                        <p style="color: var(--gray-400);">Our proprietary AI engine</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="section" style="background: var(--black);">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, $s.'_services', 'pre_headline', "What's Included") ?></p>
            <h2 class="animate-on-scroll stagger-1" style="font-size: clamp(32px, 4vw, 48px); margin-bottom: 60px;"><?= esc($content, $s.'_services', 'headline', 'Everything You Need') ?> <span class="text-accent"><?= esc($content, $s.'_services', 'headline_accent', 'to Succeed.') ?></span></h2>
            <div class="services-grid">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <?php
                    $title = sc($content, $s.'_services', "card{$i}_title");
                    $text = sc($content, $s.'_services', "card{$i}_text");
                    if (!$title && !$text) continue;
                    ?>
                    <div class="service-item animate-on-scroll stagger-<?= $i ?>">
                        <h3><?= htmlspecialchars($title) ?></h3>
                        <p><?= htmlspecialchars($text) ?></p>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <?php
    $hasAnyFaq = false;
    for ($i = 1; $i <= 4; $i++) {
        if (sc($content, $s.'_faq', "q{$i}")) { $hasAnyFaq = true; break; }
    }
    ?>
    <?php if ($hasAnyFaq): ?>
    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="faq section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, $s.'_faq', 'pre_headline', 'FAQ') ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, $s.'_faq', 'headline', $label . ' Questions,') ?> <span class="text-accent"><?= esc($content, $s.'_faq', 'headline_accent', 'Answered.') ?></span></h2>
            <div class="faq-list">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php
                    $q = sc($content, $s.'_faq', "q{$i}");
                    $a = sc($content, $s.'_faq', "a{$i}");
                    if (!$q) continue;
                    ?>
                    <div class="faq-item animate-on-scroll stagger-<?= $i + 1 ?>">
                        <div class="faq-question">
                            <h3><?= htmlspecialchars($q) ?></h3>
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <div class="faq-answer">
                            <p><?= htmlspecialchars($a) ?></p>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, $s.'_cta', 'headline', 'Ready to Get Started?') ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, $s.'_cta', 'subtitle', "Let's talk about how we can help grow your business.") ?></p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, $s.'_cta', 'cta_primary', 'Get Your Free Audit') ?> <span class="btn-arrow">&rarr;</span></a>
                <a href="contact.php" class="btn btn-secondary"><?= esc($content, $s.'_cta', 'cta_secondary', 'Talk to a Strategist') ?></a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand"><a href="index.php" class="logo">Agile & Co</a><p>AI-powered marketing for local service businesses.</p></div>
                <div class="footer-col"><h4>Services</h4><ul><?php foreach (array_slice($allServices, 0, 4) as $fs): ?><li><a href="service.php?s=<?= htmlspecialchars($fs['slug']) ?>"><?= htmlspecialchars($fs['label']) ?></a></li><?php endforeach; ?></ul></div>
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

<?php else:
// ===================== SERVICES LISTING PAGE =====================
// Default descriptions per service slug
$serviceDescriptions = [
    'seo' => 'Dominate local search results and drive organic traffic with AI-optimized SEO strategies.',
    'gads' => 'Maximize ROI with data-driven Google Ads campaigns that capture high-intent leads.',
    'meta' => 'Reach your ideal customers on Facebook and Instagram with precision-targeted ad campaigns.',
    'webdesign' => 'High-converting websites designed to turn visitors into booked jobs and paying customers.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | Agile & Co</title>
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
                <h1><?= esc($content, 'services_listing_hero', 'headline', 'AI-Powered Marketing') ?> <span class="text-accent"><?= esc($content, 'services_listing_hero', 'headline_accent', 'Services.') ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, 'services_listing_hero', 'subtitle', 'Everything your local service business needs to generate more leads, book more jobs, and grow faster — powered by our proprietary AI engine, Core.') ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, 'services_listing_hero', 'cta_primary', 'Get Your Free Audit') ?> <span class="btn-arrow">&rarr;</span></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="industries-section section">
        <div class="container">
            <h2 class="animate-on-scroll">Our Services</h2>
            <div class="industry-cards-grid">
                <?php foreach ($allServices as $svc):
                    $svcSlug = $svc['slug'];
                    $svcLabel = htmlspecialchars($svc['label']);
                    // Use subtitle from site_content if available, else fallback to hardcoded descriptions
                    $desc = sc($content, $svcSlug . '_hero', 'subtitle', $serviceDescriptions[$svcSlug] ?? 'AI-powered ' . strtolower($svc['label']) . ' services for local service businesses.');
                ?>
                    <a href="service.php?s=<?= htmlspecialchars($svcSlug) ?>" class="industry-card animate-on-scroll">
                        <h3><?= $svcLabel ?></h3>
                        <p><?= htmlspecialchars($desc) ?></p>
                        <span class="industry-card-link">Learn More &rarr;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, 'services_listing_cta', 'headline', 'Ready to Grow Your Business?') ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, 'services_listing_cta', 'subtitle', "Let's build a marketing strategy that delivers real results. Get your free audit today.") ?></p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary"><?= esc($content, 'services_listing_cta', 'cta_primary', 'Get Your Free Audit') ?> <span class="btn-arrow">&rarr;</span></a>
                <a href="contact.php" class="btn btn-secondary"><?= esc($content, 'services_listing_cta', 'cta_secondary', 'Talk to a Strategist') ?></a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand"><a href="index.php" class="logo">Agile & Co</a><p>AI-powered marketing for local service businesses.</p></div>
                <div class="footer-col"><h4>Services</h4><ul><?php foreach (array_slice($allServices, 0, 4) as $fs): ?><li><a href="service.php?s=<?= htmlspecialchars($fs['slug']) ?>"><?= htmlspecialchars($fs['label']) ?></a></li><?php endforeach; ?></ul></div>
                <div class="footer-col"><h4>Industries</h4><ul><li><a href="industry-hvac.php">HVAC</a></li><li><a href="industry-plumbing.php">Plumbing</a></li><li><a href="industry-electrical.php">Electrical</a></li><li><a href="industries.php">View All</a></li></ul></div>
                <div class="footer-col"><h4>Company</h4><ul><li><a href="about.php">About Us</a></li><li><a href="core.php">Core</a></li><li><a href="blog.php">Blog</a></li><li><a href="contact.php">Contact</a></li><li><a href="quiz.php">Free Quiz</a></li></ul></div>
            </div>
            <div class="footer-bottom"><p>&copy; 2025 Agile & Co. All rights reserved.</p><div class="footer-legal"><a href="#">Privacy Policy</a><a href="#">Terms of Service</a></div></div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
<?php endif; ?>

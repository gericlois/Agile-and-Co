<?php
require_once 'config/database.php';

$slug = trim($_GET['i'] ?? '');
if (!$slug) {
    header('Location: industries.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM industries WHERE slug = ? AND is_custom = 0");
$stmt->execute([$slug]);
$industry = $stmt->fetch();

if (!$industry) {
    header('Location: industries.php');
    exit;
}

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

$s = $slug;
$label = htmlspecialchars($industry['label']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $label ?> Marketing | Agile & Co</title>
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
                <div class="hero-badge"><?= esc($content, $s.'_hero', 'badge', $label . ' Marketing') ?></div>
                <h1><?= esc($content, $s.'_hero', 'headline', 'AI-Powered Marketing for') ?> <span class="text-accent"><?= esc($content, $s.'_hero', 'headline_accent', $label . ' Companies.') ?></span></h1>
                <p class="hero-subtitle"><?= esc($content, $s.'_hero', 'subtitle', 'More leads, more booked jobs, and a marketing strategy built specifically for ' . strtolower($industry['label']) . ' businesses.') ?></p>
                <div class="hero-ctas">
                    <a href="contact.php" class="btn btn-primary"><?= esc($content, $s.'_hero', 'cta_primary', 'Get Your Free Audit') ?> <span class="btn-arrow">&rarr;</span></a>
                    <a href="core.php" class="btn btn-secondary"><?= esc($content, $s.'_hero', 'cta_secondary', 'See How Core Works') ?></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="intro-section section">
        <div class="container">
            <div class="intro-grid">
                <div class="intro-content animate-on-scroll">
                    <p class="pre-headline"><?= esc($content, $s.'_intro', 'pre_headline', 'Why ' . $label) ?></p>
                    <h2><?= esc($content, $s.'_intro', 'headline', 'Marketing Built for') ?> <span class="text-accent"><?= esc($content, $s.'_intro', 'headline_accent', $label . '.') ?></span></h2>
                    <p><?= esc($content, $s.'_intro', 'paragraph1', '') ?></p>
                    <?php if (sc($content, $s.'_intro', 'paragraph2')): ?>
                        <p><span class="highlight"><?= esc($content, $s.'_intro', 'paragraph2') ?></span></p>
                    <?php endif; ?>
                    <?php if (sc($content, $s.'_intro', 'paragraph3')): ?>
                        <p><?= esc($content, $s.'_intro', 'paragraph3') ?></p>
                    <?php endif; ?>
                </div>
                <div class="animate-on-scroll stagger-2">
                    <div class="intro-card">
                        <h3><?= esc($content, $s.'_intro', 'card_title', 'Why ' . $label . '?') ?></h3>
                        <p><?= esc($content, $s.'_intro', 'card_text', 'We understand the unique challenges of ' . strtolower($industry['label']) . ' businesses.') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--black);"></div>

    <section class="challenges section">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, $s.'_challenges', 'pre_headline', 'Industry Challenges') ?></p>
            <h2 class="animate-on-scroll stagger-1"><?= esc($content, $s.'_challenges', 'headline', 'Common ' . $label) ?> <span class="text-accent"><?= esc($content, $s.'_challenges', 'headline_accent', 'Marketing Challenges.') ?></span></h2>
            <div class="challenges-grid">
                <?php
                $card1Title = sc($content, $s.'_challenges', 'card1_title');
                $card2Title = sc($content, $s.'_challenges', 'card2_title');
                ?>
                <?php if ($card1Title): ?>
                <div class="challenge-card animate-on-scroll stagger-2">
                    <div class="challenge-number">01</div>
                    <h3><?= htmlspecialchars($card1Title) ?></h3>
                    <p><?= esc($content, $s.'_challenges', 'card1_text') ?></p>
                    <?php if (sc($content, $s.'_challenges', 'card1_solution')): ?>
                    <div class="challenge-solution"><strong>How We Help</strong><p><?= esc($content, $s.'_challenges', 'card1_solution') ?></p></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if ($card2Title): ?>
                <div class="challenge-card animate-on-scroll stagger-3">
                    <div class="challenge-number">02</div>
                    <h3><?= htmlspecialchars($card2Title) ?></h3>
                    <p><?= esc($content, $s.'_challenges', 'card2_text') ?></p>
                    <?php if (sc($content, $s.'_challenges', 'card2_solution')): ?>
                    <div class="challenge-solution"><strong>How We Help</strong><p><?= esc($content, $s.'_challenges', 'card2_solution') ?></p></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="section-divider" style="background: var(--gray-900);"></div>

    <section class="section" style="background: linear-gradient(180deg, var(--gray-900) 0%, var(--black) 100%);">
        <div class="container">
            <p class="pre-headline animate-on-scroll"><?= esc($content, $s.'_services', 'pre_headline', 'Our Services') ?></p>
            <h2 class="animate-on-scroll stagger-1" style="font-size: clamp(32px, 4vw, 48px); margin-bottom: 60px;"><?= esc($content, $s.'_services', 'headline', 'How We Help') ?> <span class="text-accent"><?= esc($content, $s.'_services', 'headline_accent', $label . ' Businesses Grow.') ?></span></h2>
            <div class="services-grid">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php
                    $title = sc($content, $s.'_services', "card{$i}_title");
                    $text = sc($content, $s.'_services', "card{$i}_text");
                    if (!$title && !$text) continue;
                    ?>
                    <div class="service-card animate-on-scroll stagger-<?= $i + 1 ?>">
                        <h3><?= htmlspecialchars($title) ?></h3>
                        <p><?= htmlspecialchars($text) ?></p>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll"><?= esc($content, $s.'_cta', 'headline', 'Ready to Grow Your ' . $label . ' Business?') ?></h2>
            <p class="animate-on-scroll stagger-1"><?= esc($content, $s.'_cta', 'subtitle', "Let's talk about how we can help you get more leads and more booked jobs.") ?></p>
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

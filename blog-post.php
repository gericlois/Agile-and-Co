<?php
require_once 'config/database.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: blog.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> | Agile & Co Blog</title>
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

    <section class="hero" style="min-height: 40vh;">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content" style="text-align: center; max-width: 900px; margin: 0 auto;">
                <?php if ($post['tag']): ?>
                    <p class="pre-headline" style="justify-content: center;"><?= htmlspecialchars($post['tag']) ?></p>
                <?php endif; ?>
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                <p class="hero-subtitle" style="max-width: 100%; margin-left: auto; margin-right: auto;"><?= date('F j, Y', strtotime($post['created_at'])) ?></p>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--gray-900);">
        <div class="container">
            <div class="blog-post-content" style="max-width: 800px; margin: 0 auto;">
                <?php if ($post['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" style="width: 100%; border-radius: 20px; margin-bottom: 48px;">
                <?php endif; ?>
                <div class="post-body" style="font-size: 18px; color: var(--gray-300); line-height: 1.9;">
                    <?= $post['content'] ?>
                </div>
                <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--gray-700);">
                    <a href="blog.php" style="color: var(--accent); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">← Back to Blog</a>
                </div>
            </div>
        </div>
    </section>

    <section class="final-cta section">
        <div class="container">
            <h2 class="animate-on-scroll">Ready to Grow Your Business?</h2>
            <p class="animate-on-scroll stagger-1">Let's talk about how AI-powered marketing can help your service business get more leads.</p>
            <div class="cta-buttons animate-on-scroll stagger-2">
                <a href="contact.php" class="btn btn-primary">Get Your Free Audit <span class="btn-arrow">→</span></a>
                <a href="contact.php" class="btn btn-secondary">Talk to a Strategist</a>
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

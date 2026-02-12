<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Perfect Marketing Plan | Agile & Co</title>
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
            <div class="hero-content" style="text-align: center; max-width: 700px; margin: 0 auto;">
                <p class="pre-headline" style="justify-content: center;">Smart Recommender</p>
                <h1>Find Your Perfect <span class="text-accent">Marketing Plan</span></h1>
                <p class="hero-subtitle" style="margin-left: auto; margin-right: auto;">Answer 5 quick questions about your business and our AI will recommend the ideal service package — tailored to your industry, goals, and budget.</p>
            </div>
        </div>
    </section>

    <section class="quiz-section section">
        <div class="container">
            <div class="quiz-container" id="quiz-container">
                <div class="quiz-progress">
                    <div class="quiz-progress-bar" id="quiz-progress-bar"></div>
                    <span class="quiz-progress-text" id="quiz-progress-text">Question 1 of 5</span>
                </div>
                <div class="quiz-steps" id="quiz-steps"></div>
                <div class="quiz-nav" id="quiz-nav">
                    <button class="btn btn-secondary quiz-back" id="quiz-back" style="display: none;">&larr; Back</button>
                    <button class="btn btn-primary quiz-next" id="quiz-next" disabled>Next &rarr;</button>
                </div>
            </div>
            <div class="quiz-results" id="quiz-results" style="display: none;"></div>
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
    <script src="js/quiz.js"></script>
    <script src="js/chatbot.js"></script>
</body>
</html>

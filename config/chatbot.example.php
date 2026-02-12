<?php
// ============================================
// Chatbot Configuration (Google Gemini)
// ============================================
// Copy this file to chatbot.php and add your API key

// Get your free API key from: https://aistudio.google.com/apikey
define('GEMINI_API_KEY', 'YOUR_API_KEY_HERE');

// Model to use
define('GEMINI_MODEL', 'gemini-2.5-flash');

// Model for lead scoring
define('GEMINI_SCORING_MODEL', 'gemini-2.5-flash');

// Max messages per session per minute (rate limiting)
define('CHATBOT_RATE_LIMIT', 30);

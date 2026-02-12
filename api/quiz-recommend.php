<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../config/chatbot.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Rate limiting (5 per minute)
if (!isset($_SESSION['quiz_count'])) {
    $_SESSION['quiz_count'] = 0;
    $_SESSION['quiz_window'] = time();
}
if (time() - $_SESSION['quiz_window'] > 60) {
    $_SESSION['quiz_count'] = 0;
    $_SESSION['quiz_window'] = time();
}
$_SESSION['quiz_count']++;
if ($_SESSION['quiz_count'] > 5) {
    echo json_encode(['error' => 'Too many requests. Please wait a moment.']);
    exit;
}

// Check API key
if (GEMINI_API_KEY === 'YOUR_API_KEY_HERE' || empty(GEMINI_API_KEY)) {
    echo json_encode(['error' => 'API key not configured.']);
    exit;
}

// Parse input
$input = json_decode(file_get_contents('php://input'), true);
$answers = $input['answers'] ?? [];

// Validate required answer keys
$required = ['industry', 'current_marketing', 'goal', 'budget', 'timeline'];
foreach ($required as $key) {
    if (empty($answers[$key])) {
        echo json_encode(['error' => 'Please answer all questions.']);
        exit;
    }
}

// Sanitize answers
foreach ($answers as $k => $v) {
    $answers[$k] = preg_replace('/[^a-z0-9_]/', '', strtolower($v));
}

// Fetch services and industries from DB
$services = $pdo->query("SELECT label, slug FROM services WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll();
$industries = $pdo->query("SELECT label, slug FROM industries ORDER BY sort_order, id")->fetchAll();

$serviceList = implode("\n", array_map(fn($s) => "- {$s['label']} (slug: {$s['slug']})", $services));
$industryList = implode(", ", array_map(fn($i) => $i['label'], $industries));

// Human-readable answer labels
$answerLabels = [
    'industry' => [
        'hvac' => 'HVAC / Heating & Cooling', 'plumbing' => 'Plumbing',
        'electrical' => 'Electrical', 'remodeling' => 'Remodeling / Construction',
        'landscaping' => 'Landscaping / Lawn Care', 'automotive' => 'Automotive',
        'medspa' => 'Med Spa / Healthcare', 'other' => 'Other Service Business'
    ],
    'current_marketing' => [
        'nothing' => 'Not much — mostly word-of-mouth',
        'basic' => 'Basic website, maybe some social media',
        'some_ads' => 'Running some ads (Google or Facebook)',
        'full' => 'Full marketing setup (SEO, ads, website, social)'
    ],
    'goal' => [
        'more_leads' => 'Get more leads & phone calls',
        'online_presence' => 'Build a stronger online presence',
        'beat_competitors' => 'Outrank competitors in my area',
        'new_website' => 'Get a professional new website',
        'brand_awareness' => 'Increase brand awareness on social media'
    ],
    'budget' => [
        'under_1k' => 'Under $1,000/mo', '1k_3k' => '$1,000 - $3,000/mo',
        '3k_5k' => '$3,000 - $5,000/mo', '5k_plus' => '$5,000+/mo',
        'not_sure' => 'Not sure yet'
    ],
    'timeline' => [
        'asap' => 'ASAP — needs leads now', '1_3_months' => 'Within 1-3 months',
        '3_6_months' => '3-6 months', 'long_term' => 'Long-term growth'
    ]
];

// Build readable answers
$readableAnswers = '';
foreach ($required as $key) {
    $label = $answerLabels[$key][$answers[$key]] ?? $answers[$key];
    $readableAnswers .= "- " . ucwords(str_replace('_', ' ', $key)) . ": " . $label . "\n";
}

// System prompt
$systemPrompt = "You are a digital marketing strategist for Agile & Co, an AI-powered marketing agency specializing in local service businesses.

Based on a website quiz, recommend the best combination of services and explain why.

AVAILABLE SERVICES:
{$serviceList}

INDUSTRIES WE SERVE:
{$industryList}

RECOMMENDATION RULES:
- Recommend 1-3 services based on the visitor's budget, goals, and timeline
- For budgets under \$1,000/mo, recommend at most 1-2 services
- For ASAP timelines, prioritize Google Ads (immediate results) over SEO (long-term)
- For long-term growth, prioritize SEO
- For 'new website' goal, always include Web Design
- For 'brand awareness' goal, always include Meta Ads
- For 'more leads', consider both Google Ads and SEO
- Tailor the explanation to their specific industry
- Be enthusiastic but honest and concise

You MUST respond with ONLY valid JSON in this exact format:
{\"services\": [{\"slug\": \"service_slug\", \"label\": \"Service Name\", \"reason\": \"One sentence why this service fits their needs\"}], \"explanation\": \"3-4 sentence personalized explanation\", \"cta_text\": \"One compelling call-to-action sentence\"}";

$userPrompt = "QUIZ RESPONSES:\n{$readableAnswers}\nPlease recommend the best service package for this visitor.";

// Build Gemini API request
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => $userPrompt]]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.4,
        'maxOutputTokens' => 1024,
        'topP' => 0.85,
        'responseMimeType' => 'application/json'
    ]
];

// Call Gemini API
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode(['error' => 'Connection error. Please try again.']);
    exit;
}

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    $errorMsg = $errorData['error']['message'] ?? 'API error (HTTP ' . $httpCode . ')';
    echo json_encode(['error' => $errorMsg]);
    exit;
}

$data = json_decode($response, true);

// Thinking model: iterate parts to get LAST text part (skip thinking)
$parts = $data['candidates'][0]['content']['parts'] ?? [];
$replyText = null;
foreach ($parts as $part) {
    if (isset($part['text'])) {
        $replyText = $part['text'];
    }
}

if (!$replyText) {
    echo json_encode(['error' => 'No response from AI. Please try again.']);
    exit;
}

// Parse JSON response
$result = json_decode($replyText, true);

if (!$result || !isset($result['services'])) {
    if (preg_match('/\{[\s\S]*"services"[\s\S]*\}/m', $replyText, $matches)) {
        $result = json_decode($matches[0], true);
    }
}

if (!$result || !isset($result['services']) || !is_array($result['services'])) {
    echo json_encode(['error' => 'Could not parse recommendation. Please try again.']);
    exit;
}

echo json_encode([
    'services'    => $result['services'],
    'explanation' => $result['explanation'] ?? 'Based on your answers, we recommend the services above.',
    'cta_text'    => $result['cta_text'] ?? "Ready to grow? Let's talk about your custom plan."
]);

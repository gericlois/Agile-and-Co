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

// Rate limiting
if (!isset($_SESSION['chat_count'])) {
    $_SESSION['chat_count'] = 0;
    $_SESSION['chat_window'] = time();
}
if (time() - $_SESSION['chat_window'] > 60) {
    $_SESSION['chat_count'] = 0;
    $_SESSION['chat_window'] = time();
}
$_SESSION['chat_count']++;
if ($_SESSION['chat_count'] > CHATBOT_RATE_LIMIT) {
    echo json_encode(['error' => 'Too many messages. Please wait a moment.']);
    exit;
}

// Check API key
if (GEMINI_API_KEY === 'YOUR_API_KEY_HERE' || empty(GEMINI_API_KEY)) {
    echo json_encode(['error' => 'Chatbot API key not configured. Please set your Gemini API key in config/chatbot.php']);
    exit;
}

// Parse input
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
$history = $input['history'] ?? [];

if (empty($message)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// Build context from database
$services = $pdo->query("SELECT label, slug FROM services WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll();
$industries = $pdo->query("SELECT label, slug FROM industries ORDER BY sort_order, id")->fetchAll();

$serviceList = implode(', ', array_map(fn($s) => $s['label'], $services));
$industryList = implode(', ', array_map(fn($i) => $i['label'], $industries));

$serviceLinks = implode("\n", array_map(fn($s) => "- {$s['label']}: /service.php?s={$s['slug']}", $services));
$industryLinks = implode("\n", array_map(fn($i) => "- {$i['label']}: /industry.php?i={$i['slug']}", $industries));

$systemPrompt = "You are Ace, the AI assistant for Agile & Co, an AI-powered digital marketing agency that helps local service businesses grow online. Always refer to yourself as Ace when introducing yourself.

COMPANY INFO:
- Name: Agile & Co
- Focus: AI-powered marketing solutions for local service businesses
- Website sections: Homepage (/index.php), About (/about.html), Contact (/contact.php), Blog (/blog.php)

SERVICES OFFERED:
{$serviceLinks}

INDUSTRIES SERVED:
{$industryLinks}

GUIDELINES:
- Be friendly, professional, and concise (2-3 sentences max per response)
- When visitors ask about services, briefly explain and link to the relevant page
- When visitors ask about industries, mention you specialize in their field and link to the page
- For pricing or detailed questions, direct them to the contact page (/contact.php)
- You can use markdown links like [Contact Us](/contact.php) to help visitors navigate
- Never make up information about pricing, team members, or specific results
- If asked something unrelated to marketing or the business, politely redirect
- Be enthusiastic about how AI-powered marketing can help their business";

// Build Gemini API request
$contents = [];

// Add conversation history
foreach ($history as $msg) {
    $role = $msg['role'] === 'user' ? 'user' : 'model';
    $contents[] = [
        'role' => $role,
        'parts' => [['text' => $msg['text']]]
    ];
}

// Add current message
$contents[] = [
    'role' => 'user',
    'parts' => [['text' => $message]]
];

$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents' => $contents,
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 300,
        'topP' => 0.9
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
$reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

if (!$reply) {
    echo json_encode(['error' => 'No response from AI. Please try again.']);
    exit;
}

echo json_encode(['reply' => $reply]);

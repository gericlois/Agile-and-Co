<?php
require_once __DIR__ . '/chatbot.php';

function scoreLeadWithAI($pdo, $contactId) {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$contactId]);
    $contact = $stmt->fetch();

    if (!$contact) {
        return ['score' => null, 'reason' => null, 'error' => 'Contact not found'];
    }

    if (GEMINI_API_KEY === 'YOUR_API_KEY_HERE' || empty(GEMINI_API_KEY)) {
        return ['score' => null, 'reason' => null, 'error' => 'Gemini API key not configured'];
    }

    $name = trim($contact['first_name'] . ' ' . $contact['last_name']);
    $customFields = '';
    if (!empty($contact['custom_fields'])) {
        $cf = json_decode($contact['custom_fields'], true);
        if ($cf) {
            foreach ($cf as $key => $val) {
                if ($val !== '') {
                    $label = ucwords(str_replace('_', ' ', $key));
                    $customFields .= "- {$label}: {$val}\n";
                }
            }
        }
    }

    $contactSummary = "CONTACT INFORMATION:\n"
        . "- Name: {$name}\n"
        . "- Email: {$contact['email']}\n"
        . ($contact['phone'] ? "- Phone: {$contact['phone']}\n" : "- Phone: Not provided\n")
        . ($contact['company'] ? "- Company: {$contact['company']}\n" : "- Company: Not provided\n")
        . ($contact['industry'] ? "- Industry: {$contact['industry']}\n" : "- Industry: Not specified\n")
        . ($contact['message'] ? "- Message: {$contact['message']}\n" : "- Message: No message\n")
        . ($customFields ? "\nADDITIONAL FIELDS:\n{$customFields}" : "");

    $systemPrompt = "You are a lead scoring analyst for Agile & Co, an AI-powered digital marketing agency that serves local service businesses (HVAC, plumbing, electrical, landscaping, remodeling, etc.).

Your job is to analyze a contact form submission and classify the lead as HOT, WARM, or COLD.

SCORING CRITERIA:
- HOT: High intent to buy. Indicators: mentions specific services needed, has a company name, is in a target industry, detailed message showing urgency, provides phone number, mentions budget or timeline.
- WARM: Moderate interest. Indicators: some relevant details provided, in a related industry, general inquiry about services, partially filled form.
- COLD: Low intent or poor fit. Indicators: vague or off-topic message, no company info, uses generic/suspicious email, unrelated industry, very minimal information provided, appears to be spam.

IMPORTANT: You MUST respond with ONLY valid JSON in exactly this format, no markdown, no extra text:
{\"score\": \"hot\", \"reason\": \"Brief 1-2 sentence explanation\"}

The score MUST be one of: \"hot\", \"warm\", \"cold\" (lowercase).
The reason should be concise and professional, suitable for display to a sales team.";

    $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/'
        . GEMINI_SCORING_MODEL . ':generateContent?key=' . GEMINI_API_KEY;

    $payload = [
        'system_instruction' => [
            'parts' => [['text' => $systemPrompt]]
        ],
        'contents' => [
            [
                'role' => 'user',
                'parts' => [['text' => $contactSummary]]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 1024,
            'topP' => 0.8,
            'responseMimeType' => 'application/json'
        ]
    ];

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
        return ['score' => null, 'reason' => null, 'error' => 'Curl error: ' . $curlError];
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? 'HTTP ' . $httpCode;
        return ['score' => null, 'reason' => null, 'error' => 'API error: ' . $errorMsg];
    }

    $data = json_decode($response, true);
    // Thinking models return multiple parts - get the last text part (skip thinking)
    $parts = $data['candidates'][0]['content']['parts'] ?? [];
    $replyText = null;
    foreach ($parts as $part) {
        if (isset($part['text'])) {
            $replyText = $part['text'];
        }
    }

    if (!$replyText) {
        return ['score' => null, 'reason' => null, 'error' => 'No response from Gemini'];
    }

    $result = json_decode($replyText, true);

    if (!$result || !isset($result['score'])) {
        if (preg_match('/\{[^}]*"score"\s*:\s*"(hot|warm|cold)"[^}]*\}/i', $replyText, $matches)) {
            $result = json_decode($matches[0], true);
        }
    }

    $score = strtolower($result['score'] ?? '');
    $reason = $result['reason'] ?? '';

    if (!in_array($score, ['hot', 'warm', 'cold'])) {
        return ['score' => null, 'reason' => null, 'error' => 'Invalid score returned: ' . $score];
    }

    $updateStmt = $pdo->prepare("UPDATE contacts SET lead_score = ?, lead_score_reason = ?, lead_scored_at = NOW() WHERE id = ?");
    $updateStmt->execute([$score, $reason, $contactId]);

    return ['score' => $score, 'reason' => $reason, 'error' => null];
}

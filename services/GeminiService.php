<?php

if (!function_exists('_geminiApiKey')) {

    function _geminiApiKey(): string
    {
        return trim(env('GEMINI_API_KEY', ''));
    }

}

function geminiCall(string $system, string $user, int $maxTokens = 1000, float $temperature = 0.5): array {
    $apiKey = _geminiApiKey();
    if (!$apiKey) return ['success' => false, 'error' => 'Gemini API key not configured'];

    $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
    $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $payload = json_encode([
        'system_instruction' => [
            'parts' => [['text' => $system]]
        ],
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $user]]]
        ],
        'generationConfig' => [
            'maxOutputTokens' => $maxTokens,
            'temperature'     => $temperature,
        ],
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);

    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err)      return ['success' => false, 'error' => 'cURL: ' . $err];
    if ($code !== 200) {
        $b = json_decode($raw, true);
        return ['success' => false, 'error' => $b['error']['message'] ?? "HTTP {$code}", 'raw' => $raw];
    }

    $body = json_decode($raw, true);
    $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (trim($text) === '') {
        $reason = $body['candidates'][0]['finishReason'] ?? 'unknown';
        return ['success' => false, 'error' => "Empty response (finishReason: {$reason})"];
    }

    return ['success' => true, 'text' => trim($text)];
}

function geminiJSON(string $system, string $user, int $maxTokens = 1200): array {
    $r = geminiCall($system, $user, $maxTokens, 0.2);
    if (!$r['success']) return $r;

    $text = trim(preg_replace(['/^```(json)?\s*/m', '/```\s*$/m'], '', $r['text']));

    $data = json_decode($text, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error'   => 'JSON parse error: ' . json_last_error_msg(),
            'raw'     => $text,
        ];
    }

    return ['success' => true, 'data' => $data];
}
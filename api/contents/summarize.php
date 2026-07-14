<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$text = trim($_POST['original_text'] ?? '');

if ($text == '') {
    errorResponse('Original text is required.');
}

$system = "
You are an expert video summarizer.
Your task is to summarize the content of a video based on its transcript or description.

Instructions:

- Return ONLY the summary.
- Write in clear paragraph form.
- Keep the summary between 150 and 300 words.
- Capture the main topic, important explanations, key facts, and conclusion.
- Ignore greetings, advertisements, sponsor messages, and repeated sentences.
Return only the summary text.
";

$result = geminiCall(
    $system,
    $text,
    1500,
    0.3
);

if (!$result['success']) {

    errorResponse($result['error']);

}

successResponse([
    'summary' => $result['text']
]);
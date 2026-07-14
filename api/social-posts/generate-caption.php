<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {

    errorResponse('Invalid request.');

}

$uuid = trim($_POST['uuid'] ?? '');

if ($uuid == '') {

    errorResponse('Content UUID is required.');

}

$content = find(
    $pdo,
    'contents',
    [
        'uuid' => $uuid
    ]

);


$system = <<<PROMPT
You are a professional social media marketer.

Generate ONLY valid JSON.

{
    "caption":"",
    "image_prompt":"",
    "video_prompt":"",
    "hashtags":"",
    "keywords":""
}

Rules:

- caption should be engaging.
- image_prompt should be detailed.
- video_prompt should be detailed.
- hashtags must be comma separated.
- keywords must be comma separated.
- No markdown.
- No code block.
- Output JSON only.
PROMPT;

$user = "

Title:
{$content['title']}

Summary:
{$content['ai_summary']}

";

$result = geminiJSON($system, $user);

if (!$result['success']) {
    errorResponse($result['error']);

}

apiSuccess($result['data']);
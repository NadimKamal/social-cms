<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {

    errorResponse('Invalid request.');

}

$uuids = $_POST['uuids'] ?? [];

if (!is_array($uuids) || empty($uuids)) {
    errorResponse('Please select at least one content.');
}

$placeholders = implode(',', array_fill(0, count($uuids), '?'));

$stmt = $pdo->prepare("
    SELECT
        title,
        ai_summary
    FROM contents
    WHERE uuid IN ($placeholders)
");

$stmt->execute($uuids);

$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contents)) {
    errorResponse('Selected contents not found.');
}

/*
|--------------------------------------------------------------------------
| Build Gemini Input
|--------------------------------------------------------------------------
*/

$user = '';

$count = 1;

foreach ($contents as $content) {

    $user .= <<<TEXT

=====================
CONTENT {$count}

Title: {$content['title']}

AI Summary: {$content['ai_summary']}

TEXT;
    $count++;
}

/*
|--------------------------------------------------------------------------
| Gemini Prompt
|--------------------------------------------------------------------------
*/

$system = <<<PROMPT
You are an expert Digital Marketing Strategist and Social Media Content Creator.

You will receive multiple AI-generated content summaries.

Your task is to analyze ALL summaries together and produce ONE unified, high-quality social media post.

Instructions:

- Read every content carefully.
- Merge related ideas naturally.
- Remove duplicated information.
- Keep only the most important marketing messages.
- Create ONE post, not multiple.
- Never mention Content 1, Content 2, etc.
- Write naturally as if the information originally came from one source.

Return ONLY valid JSON.
{
    "caption": "",
    "image_prompt": "",
    "hashtags": "",
    "keywords": ""
}

Rules:

caption
- Engaging and professional.
- Ready for Facebook, LinkedIn and Instagram.
- Can contain emojis.
- Around 120-250 words.

image_prompt
- A detailed prompt for an AI image generator.
- Describe one attractive marketing image that represents the complete combined message.
- Do NOT mention text overlays unless necessary.

hashtags
- Comma separated.
- Between 8 and 15 hashtags.

keywords
- Comma separated SEO keywords.
- Between 8 and 15 keywords.

Output JSON only.

No markdown.

No explanations.

No code block.
PROMPT;

/*
|--------------------------------------------------------------------------
| Generate Caption
|--------------------------------------------------------------------------
*/

$result = geminiJSON($system, $user);

if (!$result['success']) {
    errorResponse($result['error']);
}

/*
|--------------------------------------------------------------------------
| Generate Image
|--------------------------------------------------------------------------
*/
$image = geminiGenerateImage(
    $result['data']['image_prompt']
);

if ($image['success']) {
    $result['data']['image_path'] = $image['image_path'];

} else {

    $result['data']['image_path'] = '';

}

/*
|--------------------------------------------------------------------------
| Remove Internal Prompt
|--------------------------------------------------------------------------
*/

unset($result['data']['image_prompt']);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

apiSuccess($result['data']);
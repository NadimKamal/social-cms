<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$categories = $pdo->query("
SELECT
    id,
    title
FROM content_categories
WHERE is_active=1
ORDER BY title
")->fetchAll(PDO::FETCH_ASSOC);

$categoryList = '';

foreach ($categories as $category)
{
    $categoryList .= "{$category['id']} = {$category['title']}\n";
}

$text = trim($_POST['original_text'] ?? '');
if ($text == '') {
    errorResponse('Original text is required.');
}

$system = <<<PROMPT
You are an expert Digital Marketing AI.

Your job is to:

1. Summarize the content.
2. Decide which category best matches the content.

Available Categories:

{$categoryList}

Rules:

- Choose ONLY ONE category id.
- category_id MUST be one of the ids above.
- Return ONLY valid JSON.

{
    "summary":"",
    "category_id":""
}

Summary should be 150-300 words.

No markdown.

No explanation.

Only JSON.
PROMPT;

$result = geminiJSON(
    $system,
    $text,
    1500
);

if (!$result['success']) {
    errorResponse($result['error']);
}

successResponse([
    'summary'      => $result['data']['summary'],
    'category_id'  => $result['data']['category_id']
]);
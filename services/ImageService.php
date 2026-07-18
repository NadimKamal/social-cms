<?php

if (!function_exists('geminiGenerateImage')) {

    function geminiGenerateImage(string $prompt): array
    {

        $apiKey = trim(env('GEMINI_API_KEY'));
        if ($apiKey == '') {
            return [
                'success' => false,
                'error' => 'Gemini API key not configured.'
            ];
        }
        $model = env('GEMINI_IMAGE_MODEL', 'gemini-3.1-flash-image');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = json_encode([
            'contents' => [[
                'parts' => [[
                    'text' => $prompt
                ]]
            ]]

        ], JSON_UNESCAPED_UNICODE);
        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [

                'Content-Type: application/json'

            ],
            CURLOPT_TIMEOUT => 180

        ]);

        $raw = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return [

                'success' => false,
                'error' => $error
            ];
        }

        if ($http != 200) {
            return [
                'success' => false,
                'error' => $raw
            ];
        }

        $body = json_decode($raw, true);
        $inlineData =
            $body['candidates'][0]['content']['parts'][0]['inlineData']['data']
            ?? null;

        if (!$inlineData) {
            return [

                'success' => false,
                'error' => 'No image returned from Gemini.'

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Save Image
        |--------------------------------------------------------------------------
        */

        $directory = ROOT_PATH . '/uploads/social_posts/images/';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filename =
            uniqid('social_post_') . '.png';

        file_put_contents(

            $directory . $filename,

            base64_decode($inlineData)

        );

        return [

            'success' => true,

            'image_path' =>
                'uploads/social_posts/images/' .
                $filename

        ];

    }

}
<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

/* Logout User */
logout();

/* Response */
apiSuccess([
    'message' => 'Logged out successfully.'
]);
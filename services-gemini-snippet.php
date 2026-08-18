<?php

// Add this array entry inside the existing config/services.php file
// (inside the returned array, alongside 'mailgun', 'postmark', etc.):

'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
],

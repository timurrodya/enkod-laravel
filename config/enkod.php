<?php

return [
    /*
     * API token to Enkod
     */
    'apiKey'  => env('ENKOD_API_KEY', ''),
    'baseurl' => env('ENKOD_BASE_URL', 'https://api.enkod.ru/'),
    'version' => env('ENKOD_VERSION', 'v1'),
];

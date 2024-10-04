<?php

return [
    'directory' => '/upload/',

    'rate-limit' => [
        'upload' => [
            'per-ip-per-day' => 200,
            'per-ip-per-minute' => 40,
            'everyone-per-day' => 4000,
        ],
        'download' => [
            'per-ip-per-day' => 100,
            'per-ip-per-minute' => 20,
            'everyone-per-day' => 2000,
        ],
    ],
];

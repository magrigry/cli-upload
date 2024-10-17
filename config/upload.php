<?php

return [

    'directory' => '/upload/',

    // kB, MB, GB, TB, PB
    'max_capacity' => env('UPLOAD_MAX_CAPACITY', '9GB'),
    'max_capacity_per_ip' => env('UPLOAD_MAX_CAPACITY_PER_IP', '1GB'),

    'rate-limit' => [
        'upload' => [
            'per-ip-per-day' => env('UPLOAD_PER_IP_PER_DAY', 50),
            'per-ip-per-minute' => env('UPLOAD_PER_IP_PER_MINUTE', 10),
            'everyone-per-day' => env('UPLOAD_EVERYONE_PER_DAY', 1000),
        ],
        'download' => [
            'per-ip-per-day' => env('DOWNLOAD_PER_IP_PER_DAY', 100),
            'per-ip-per-minute' => env('DOWNLOAD_PER_IP_PER_MINUTE', 20),
            'everyone-per-day' => env('DOWNLOAD_EVERYONE_PER_DAY', 2000),
        ],
    ],
];

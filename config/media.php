<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Media Disk
    |--------------------------------------------------------------------------
    |
    | The default filesystem disk to use for media uploads.
    |
    */
    'disk' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Recommended Image Dimensions
    |--------------------------------------------------------------------------
    |
    | Default dimensions applied when a model specifies enforcement or falls back
    | to default settings. Background padding color is hex without hash.
    |
    */
    'default_dimensions' => [
        'width' => 1000,
        'height' => 1000,
        'bg_color' => 'ffffff',
        'quality' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    */
    'allowed_mimes' => [
        'image' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ],
        'video' => [
            'video/mp4',
            'video/quicktime',
            'video/webm',
            'video/x-msvideo',
        ],
        'document' => [
            'application/pdf',
        ],
    ],
];

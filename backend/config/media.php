<?php

return [
    'driver' => env(
        'MEDIA_STORAGE_DRIVER',
        env('APP_ENV') === 'production' ? 'cloudinary' : 'local',
    ),

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'folder' => trim(env('CLOUDINARY_FOLDER', 'gorkhali-khabar'), '/'),
    ],
];

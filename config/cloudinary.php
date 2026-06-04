<?php
return [
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_PUBLIC'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'upload_folder' => env('CLOUDINARY_UPLOAD_FOLDER', 'bellevie_hotel'),
    'url'           => env('CLOUDINARY_URL'),

    /*
     | UNSIGNED upload preset for the widget.
     | Create at: Cloudinary Dashboard → Settings → Upload → Upload presets
     | Set mode to "Unsigned".
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', ''),
];

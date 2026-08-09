<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),
    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        // القرص العام (صور الأغلفة والصور): محلي في التطوير، ويتحوّل إلى Cloudflare R2
        // في الإنتاج بضبط PUBLIC_DISK_DRIVER=s3 — دون أي تعديل في المتحكّمات أو القوالب.
        'public' => [
            'driver'                  => env('PUBLIC_DISK_DRIVER', 'local'),
            'root'                    => storage_path('app/public'),
            'url'                     => env('PUBLIC_DISK_URL', env('APP_URL').'/storage'),
            'visibility'              => 'public',
            'throw'                   => false,
            // تُستخدم الحقول التالية فقط عندما PUBLIC_DISK_DRIVER=s3 (R2) —
            'key'                     => env('R2_ACCESS_KEY_ID'),
            'secret'                  => env('R2_SECRET_ACCESS_KEY'),
            'region'                  => env('R2_REGION', 'auto'),
            'bucket'                  => env('R2_PUBLIC_BUCKET'),
            'endpoint'                => env('R2_ENDPOINT'),
            'use_path_style_endpoint' => true,
        ],

        // قرص ملفات الكتب (PDF) الخاص: محلي في التطوير، R2 خاص في الإنتاج (BOOKS_DISK_DRIVER=s3).
        // يُقدَّم عبر BookFileController الذي يتحوّل تلقائياً إلى روابط موقّتة موقّعة عند s3.
        'books_private' => [
            'driver'                  => env('BOOKS_DISK_DRIVER', 'local'),
            'root'                    => storage_path('app/private/books'),
            'visibility'              => 'private',
            'throw'                   => false,
            'key'                     => env('R2_ACCESS_KEY_ID'),
            'secret'                  => env('R2_SECRET_ACCESS_KEY'),
            'region'                  => env('R2_REGION', 'auto'),
            'bucket'                  => env('R2_PRIVATE_BUCKET'),
            'endpoint'                => env('R2_ENDPOINT'),
            'use_path_style_endpoint' => true,
        ],

        // قرص خاص لملفات الكتب (PDF): لا رابط عام ولا symlink — يُقدَّم عبر BookFileController فقط
        'books_private' => [
            'driver' => 'local',
            'root' => storage_path('app/private/books'),
            'visibility' => 'private',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

       'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

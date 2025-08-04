<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    */

    'colors' => [
        'primary' => '#344D95',
        'secondary' => '#3B82F6',
    ],

    //
    'core' => [
        'minPhpVersion' => '8.2.0',
    ],
    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'gd',
            'tokenizer',
            'JSON',
            'cURL',
            'fileinfo',
            'xml',
            'ctype',
            'bcmath',
            'dom',
            'filter',
            'session',
            'zip',
            'intl',
            'exif',
            'sqlite3',
            'pcre',
            'hash',
            'iconv',
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list bellow.
    |
    */
    'permissions' => [
        'storage/framework/' => '775',
        'storage/framework/cache/' => '775',
        'storage/framework/sessions/' => '775',
        'storage/framework/views/' => '775',
        'storage/app/' => '775',
        'storage/app/public/' => '775',
        'storage/logs/' => '775',
        'bootstrap/cache/' => '775',
        'public/storage/' => '755',

    ],

    // environment

    'environment' => [
        'form' => [
            'rules' => [
                'environment' => 'required|string|max:50',
                'environment_custom' => 'required_if:environment,other|max:50',
                'app_debug' => 'required|string',
                'app_log_level' => 'required|string|max:50',
                'app_url' => 'required|url',
                'app_locale' => 'required|string|in:en,id',
                'database_connection' => 'required|in:mysql,sqlite',
                'database_hostname' => 'nullable|string|max:50',
                'database_port' => 'nullable|numeric',
                'database_name' => 'required|string|max:50',
                'database_username' => 'nullable|string|max:50',
                'database_password' => 'nullable|string|max:50',
                // Email configuration validation rules
                'mail_mailer' => 'required|string|in:smtp',
                'mail_host' => 'nullable|string|max:100',
                'mail_port' => 'nullable|numeric|min:1|max:65535',
                'mail_username' => 'nullable|string|max:100',
                'mail_password' => 'nullable|string|max:100',
                'mail_encryption' => 'nullable|string|in:tls,ssl',
                'mail_from_address' => 'email|max:100',
            ],
        ],
    ],

    'env' => 'BROADCAST_CONNECTION=log' . "\n" .
        'CACHE_STORE=database' . "\n" .
        'CACHE_PREFIX=' . "\n" .
        'FILESYSTEM_DISK=local' . "\n" .
        'QUEUE_CONNECTION=sync' . "\n" .
        'SESSION_DRIVER=database' . "\n" .
        'SESSION_LIFETIME=120' . "\n" .
        'SESSION_ENCRYPT=false' . "\n" .
        'SESSION_PATH=/' . "\n" .
        'SESSION_DOMAIN=null' . "\n\n" .
        'MEMCACHED_HOST=127.0.0.1' . "\n\n" .
        'REDIS_CLIENT=phpredis' . "\n" .
        'REDIS_HOST=127.0.0.1' . "\n" .
        'REDIS_PASSWORD=null' . "\n" .
        'REDIS_PORT=6379' . "\n\n" .
        'AWS_ACCESS_KEY_ID=' . "\n" .
        'AWS_SECRET_ACCESS_KEY=' . "\n" .
        'AWS_DEFAULT_REGION=us-east-1' . "\n" .
        'AWS_BUCKET=' . "\n" .
        'AWS_USE_PATH_STYLE_ENDPOINT=false' . "\n\n" .
        'PUSHER_APP_ID=' . "\n" .
        'PUSHER_APP_KEY=' . "\n" .
        'PUSHER_APP_SECRET=' . "\n" .
        'PUSHER_HOST=' . "\n" .
        'PUSHER_PORT=443' . "\n" .
        'PUSHER_SCHEME=https' . "\n" .
        'PUSHER_APP_CLUSTER=mt1' . "\n\n" .
        'VITE_APP_NAME="${APP_NAME}"' . "\n" .
        'VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"' . "\n" .
        'VITE_PUSHER_HOST="${PUSHER_HOST}"' . "\n" .
        'VITE_PUSHER_PORT="${PUSHER_PORT}"' . "\n" .
        'VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"' . "\n" .
        'VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"',

    // profil perusahaan

    'profil_perusahaan' => [
        'nama_perusahaan' => 'required|string|min:2|max:100',
        'logo_perusahaan' => [
            'nullable',
            'image',
            'mimes:jpeg,png,jpg,webp,svg',
            'max:5120', // Max 5MB
            'dimensions:min_width=100,min_height=100'
        ],
        'deskripsi_perusahaan' => 'nullable|string|max:1000',
        'alamat_perusahaan' => 'required|string|min:10|max:200',
        'link_alamat_perusahaan' => 'nullable|url|max:255',
        'email_perusahaan' => 'required|email|max:50',
    ],

    // feature toggles 
    'feature_toggles' => [
        'features' => 'nullable|array',
        'features.*' => 'boolean',
    ],

    // super admin
    'super_admin' => [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8|confirmed',
        'email_verified_at' => 'nullable|datetime',
        'include_dummy_data' => 'nullable|boolean',
    ],
];

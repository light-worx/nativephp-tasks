<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    | Unique reverse-DNS identifier for your app.
    | Must match NATIVEPHP_APP_ID in your .env.
    */
    'app_id' => env('NATIVEPHP_APP_ID', 'com.lightworx.tasks'),

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    */
    'version'      => env('NATIVEPHP_APP_VERSION', 'DEBUG'),
    'version_code' => env('NATIVEPHP_APP_VERSION_CODE', 1),

    /*
    |--------------------------------------------------------------------------
    | Orientation
    |--------------------------------------------------------------------------
    */
    'orientation' => [
        'android' => [
            'portrait'        => true,
            'upside_down'     => false,
            'landscape_left'  => false,
            'landscape_right' => false,
        ],
        'iphone' => [
            'portrait'        => true,
            'upside_down'     => false,
            'landscape_left'  => false,
            'landscape_right' => false,
        ],
    ],

    'cleanup_env_keys' => [
        'AWS_*',
        'DO_SPACES_*',
        'NATIVEPHP_UPDATER_PATH',
        'NATIVEPHP_APPLE_ID',
        'NATIVEPHP_APPLE_ID_PASS',
        'NATIVEPHP_APPLE_TEAM_ID',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    | Declare the Android permissions your app requires.
    */
    'permissions' => [
        'internet',         // Required for API calls
        'vibrate',          // Nice haptic feedback on actions
    ],

    /*
    |--------------------------------------------------------------------------
    | Hot Reload Paths
    |--------------------------------------------------------------------------
    */
    'hot_reload' => [
        'watch_paths' => [
            'app',
            'routes',
            'config',
            'resources/views',
            'public',
        ],
    ],

];

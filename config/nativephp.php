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

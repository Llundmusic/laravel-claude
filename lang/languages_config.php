<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Language Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the metadata for all supported languages in the
    | application. When you add a new language directory, add its configuration
    | here and run 'php artisan languages:sync' to update the database.
    |
    */

    'languages' => [
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'sort_order' => 1,  // Currently unused - languages are sorted alphabetically
        ],
        'nb' => [
            'name' => 'Norwegian',
            'native_name' => 'Norsk',
            'sort_order' => 2,  // Currently unused - languages are sorted alphabetically
        ],
        
        // Add more languages here as you create their directories
        // Languages are currently sorted alphabetically by native_name
        // The sort_order field is kept for future use if custom ordering is needed
        //
        // Example for German:
        // 'de' => [
        //     'name' => 'German',
        //     'native_name' => 'Deutsch',
        //     'sort_order' => 4,  // Currently unused
        // ],
        
        // Example for French:
        // 'fr' => [
        //     'name' => 'French',
        //     'native_name' => 'Français',
        //     'sort_order' => 5,  // Currently unused
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | These values are used when a language directory exists but isn't
    | configured in the 'languages' array above.
    |
    | Note: Languages are currently sorted alphabetically by native_name,
    | sort_order is kept for future use but not currently applied.
    |
    */
    'defaults' => [
        'name' => null,        // Will use ucfirst($code) if null
        'native_name' => null, // Will use ucfirst($code) if null
        'sort_order' => 999,   // Currently unused - kept for future use
    ],
];
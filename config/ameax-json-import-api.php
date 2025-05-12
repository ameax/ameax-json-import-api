<?php

// config for Ameax/AmeaxJsonImportApi
return [
    /*
    |--------------------------------------------------------------------------
    | Ameax API Key
    |--------------------------------------------------------------------------
    |
    | This is the API key used to authenticate with the Ameax API.
    |
    */
    'api_key' => env('AMEAX_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Ameax API Host
    |--------------------------------------------------------------------------
    |
    | This is the base URL of your Ameax API instance.
    | For production: https://your-database.ameax.de
    | For local development: http://your-database.ameax.localhost
    |
    */
    'host' => env('AMEAX_API_HOST', 'https://your-database.ameax.de'),

    /*
    |--------------------------------------------------------------------------
    | JSON Schema Path
    |--------------------------------------------------------------------------
    |
    | Path to the JSON schema files. By default, it will look in the package's
    | resources/schemas directory. You can override this to use your own schemas.
    |
    */
    'schemas_path' => null,
];

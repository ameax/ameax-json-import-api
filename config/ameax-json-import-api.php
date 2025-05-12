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
    | Ameax Database Name
    |--------------------------------------------------------------------------
    |
    | This is your Ameax database name used in the API URL:
    | https://{database_name}.ameax.de/rest-api
    |
    */
    'database_name' => env('AMEAX_DATABASE_NAME'),

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

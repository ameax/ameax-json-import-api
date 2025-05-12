# Ameax JSON Import API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ameax/ameax-json-import-api.svg?style=flat-square)](https://packagist.org/packages/ameax/ameax-json-import-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ameax/ameax-json-import-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ameax/ameax-json-import-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ameax/ameax-json-import-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ameax/ameax-json-import-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ameax/ameax-json-import-api.svg?style=flat-square)](https://packagist.org/packages/ameax/ameax-json-import-api)

A PHP package for sending data to Ameax via their JSON API. This package provides an easy way to create and send organization data to the Ameax API, with built-in validation according to the JSON schema.

## Installation

You can install the package via composer:

```bash
composer require ameax/ameax-json-import-api
```

## Configuration

### Laravel

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ameax-json-import-api-config"
```

This is the contents of the published config file:

```php
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
```

Make sure to add your Ameax API key and database name to your .env file:

```
AMEAX_API_KEY=your-api-key
AMEAX_DATABASE_NAME=your-database-name
```

### Non-Laravel PHP Projects

For non-Laravel projects, you'll need to instantiate the client directly:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

$client = new AmeaxJsonImportApi('your-api-key', 'your-database-name');
```

## Usage

### Organizations

Here's how to create and send an organization to Ameax:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Laravel: Use the facade
$client = app('ameax-json-import-api');

// OR create an instance directly
// $client = new AmeaxJsonImportApi('your-api-key', 'your-database-name');

try {
    // Create a new organization with required fields
    $organization = $client->createOrganization(
        'ACME Corporation',  // name
        '12345',             // postal_code
        'Berlin',            // locality
        'DE'                 // country (ISO 3166-1 alpha-2)
    );
    
    // Add optional additional organization data
    $organization['street'] = 'Main Street';
    $organization['house_number'] = '123';
    $organization['email'] = 'info@acme-corp.com';
    
    // Add a contact person to the organization
    $organization = $client->addOrganizationContact(
        $organization,
        'John',              // first_name
        'Doe',               // last_name
        [
            'email' => 'john.doe@acme-corp.com',
            'phone' => '+49 30 123456789',
            'job_title' => 'CEO'
        ]
    );
    
    // Send the organization data to Ameax
    $response = $client->sendOrganization($organization);
    
    // Handle the successful response
    print_r($response);
    
} catch (ValidationException $e) {
    // Handle validation errors
    print_r($e->getErrors());
} catch (\Exception $e) {
    // Handle other errors
    echo $e->getMessage();
}
```

## JSON Schema Validation

This package validates your data against the Ameax JSON schema before sending it to the API. The validation uses the official Ameax JSON schemas to ensure your data is valid.

### Custom Schemas

You can use your own schemas by setting the `schemas_path` in the config:

```php
// In Laravel, set this in your published config file
'schemas_path' => storage_path('app/schemas'),

// For non-Laravel projects
config(['ameax-json-import-api.schemas_path' => '/path/to/your/schemas']);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Schmidt](https://github.com/69188126+ms-aranes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

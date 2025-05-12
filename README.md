# Ameax JSON Import API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ameax/ameax-json-import-api.svg?style=flat-square)](https://packagist.org/packages/ameax/ameax-json-import-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ameax/ameax-json-import-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ameax/ameax-json-import-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ameax/ameax-json-import-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ameax/ameax-json-import-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ameax/ameax-json-import-api.svg?style=flat-square)](https://packagist.org/packages/ameax/ameax-json-import-api)

A framework-agnostic PHP package for sending data to Ameax via their JSON API. This package provides an easy way to create and send organization data to the Ameax API, with built-in validation according to the JSON schema.

## Installation

You can install the package via composer:

```bash
composer require ameax/ameax-json-import-api
```

## Basic Usage

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Initialize the client
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);

// Create an organization
$organization = $client->createOrganization(
    'ACME Corporation',  // name
    '12345',             // postal_code
    'Berlin',            // locality
    'DE'                 // country
);

// Add a contact
$organization = $client->addOrganizationContact(
    $organization,
    'John',
    'Doe',
    ['email' => 'john.doe@example.com']
);

// Send to Ameax
try {
    $response = $client->sendOrganization($organization);
    echo "Success!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Creating and Sending Organizations

You can create organizations with the required fields and then add additional data:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

$client = new AmeaxJsonImportApi('your-api-key', 'https://your-database.ameax.de');

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

You can use your own schemas by specifying the schemas path when initializing the client:

```php
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de',
    '/path/to/your/schemas'
);
```

## Documentation

For more detailed documentation, see the [docs](docs) directory:

- [Installation](docs/installation.md)
- [Organization API](docs/organizations.md)
- [API Reference](docs/api-reference.md)

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
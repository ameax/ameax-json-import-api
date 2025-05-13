# Installation

This package can be installed via Composer. It requires PHP 8.2 or higher.

## Requirements

- PHP 8.2 or higher
- Composer

## Installation Steps

### Via Composer

```bash
composer require ameax/ameax-json-import-api
```

### Version Constraints

When requiring this package, you can specify version constraints:

```bash
# Install the latest 1.* version
composer require ameax/ameax-json-import-api:^1.0

# Install a specific version
composer require ameax/ameax-json-import-api:1.0.0
```

For more details on versioning, see the [Versioning Guide](versioning.md).

## Basic Usage

After installing the package, you can use it in your PHP project:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Initialize the client with your API key and host
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);

// Create and send an organization
$organization = $client->createOrganization(
    'ACME Corporation',
    '12345',
    'Berlin',
    'DE'
);

$response = $client->sendOrganization($organization);
```

## JSON Schema Files

The package includes JSON schema files for validating your data before sending it to the Ameax API. By default, it uses the schema files in the `resources/schemas` directory of the package.

### Custom Schema Path

If you want to use your own schema files, you can specify the path when initializing the client:

```php
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de',
    '/path/to/your/schemas'
);
```

## Environment Flexibility

You can use different hosts for different environments:

### Production
```php
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);
```

### Local Development
```php
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'http://your-database.ameax.localhost'
);
```

### Testing
```php
$client = new AmeaxJsonImportApi(
    'test-api-key',
    'http://test-ameax-api'
);
```
# Installation

This package can be installed via Composer. It requires PHP 8.2 or higher.

## Requirements

- PHP 8.2 or higher
- Composer
- Laravel 10.0+ (for Laravel integration)

## Installation Steps

### Via Composer

```bash
composer require ameax/ameax-json-import-api
```

### Laravel Setup

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag="ameax-json-import-api-config"
```

2. Add your credentials to your `.env` file:

```
AMEAX_API_KEY=your-api-key
AMEAX_DATABASE_NAME=your-database-name
```

3. The package will automatically register the service provider and facade.

### Non-Laravel Setup

For non-Laravel PHP projects, you'll need to require the package via Composer and then instantiate the client manually:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

$client = new AmeaxJsonImportApi('your-api-key', 'your-database-name');
```

## JSON Schema Files

The package includes JSON schema files for validating your data before sending it to the Ameax API. By default, it uses the schema files in the `resources/schemas` directory of the package.

If you want to use your own schema files, you can specify the path in the config file (for Laravel) or when initializing the client (for non-Laravel projects).

### Custom Schema Path in Laravel

In your published config file:

```php
'schemas_path' => storage_path('app/schemas'),
```

### Custom Schema Path in Non-Laravel Projects

Create a config array and pass it to the client:

```php
$config = [
    'schemas_path' => '/path/to/your/schemas',
];

$client = new AmeaxJsonImportApi('your-api-key', 'your-database-name', $config);
```
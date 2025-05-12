# API Reference

This document provides a reference for all classes and methods available in the Ameax JSON Import API package.

## Main Classes

### `Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi`

The main client class for interacting with the Ameax API.

#### Constructor

```php
public function __construct(string $apiKey, string $databaseName)
```

- `$apiKey`: Your Ameax API key for authentication
- `$databaseName`: Your Ameax database name (used in the API URL)

#### Methods

**sendOrganization**

```php
public function sendOrganization(array $organization): array
```

Sends organization data to the Ameax API.

- `$organization`: An array containing the organization data
- Returns: The API response as an array
- Throws: `ValidationException` if validation fails, `\Exception` if the request fails

**createOrganization**

```php
public function createOrganization(string $name, string $postalCode, string $locality, string $country): array
```

Creates a new organization with the required fields.

- `$name`: Organization name
- `$postalCode`: Postal/ZIP code
- `$locality`: City or town
- `$country`: ISO 3166-1 alpha-2 country code
- Returns: An array containing the basic organization data

**addOrganizationContact**

```php
public function addOrganizationContact(array $organization, string $firstName, string $lastName, array $additionalData = []): array
```

Adds a contact person to an organization.

- `$organization`: The organization array
- `$firstName`: Contact person's first name
- `$lastName`: Contact person's last name
- `$additionalData`: Optional additional contact data (email, phone, etc.)
- Returns: The updated organization array with the new contact added

### `Ameax\AmeaxJsonImportApi\Schema\OrganizationSchema`

Helper class for working with organization schemas.

#### Constants

- `DOCUMENT_TYPE`: The document type for organizations ("ameax_organization_account")
- `SCHEMA_VERSION`: The schema version ("1.0")

#### Static Methods

**create**

```php
public static function create(string $name, string $postalCode, string $locality, string $country): array
```

Creates a new organization schema with required fields.

- `$name`: Organization name
- `$postalCode`: Postal/ZIP code
- `$locality`: City or town
- `$country`: ISO 3166-1 alpha-2 country code
- Returns: An array containing the basic organization data

**addContact**

```php
public static function addContact(array $organization, string $firstName, string $lastName, array $additionalData = []): array
```

Adds a contact to an organization.

- `$organization`: The organization array
- `$firstName`: Contact person's first name
- `$lastName`: Contact person's last name
- `$additionalData`: Optional additional contact data
- Returns: The updated organization array with the new contact added

### `Ameax\AmeaxJsonImportApi\Exceptions\ValidationException`

Exception thrown when JSON schema validation fails.

#### Methods

**getErrors**

```php
public function getErrors(): array
```

Gets the validation error messages.

- Returns: An array of validation error messages

## Laravel Integration

### Service Provider

The package includes a service provider that automatically registers the client with your Laravel application:

```php
Ameax\AmeaxJsonImportApi\AmeaxJsonImportApiServiceProvider::class
```

### Facade

For Laravel applications, you can use the facade to access the client:

```php
use Ameax\AmeaxJsonImportApi\Facades\AmeaxJsonImportApi;

$organization = AmeaxJsonImportApi::createOrganization(
    'ACME Corporation',
    '12345',
    'Berlin',
    'DE'
);
```

### Dependency Injection

You can also use dependency injection to get the client instance:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

public function store(Request $request, AmeaxJsonImportApi $client)
{
    $organization = $client->createOrganization(
        $request->name,
        $request->postal_code,
        $request->locality,
        $request->country
    );
    
    // ...
}
```

### Service Container

Or you can resolve the client from the service container:

```php
$client = app('ameax-json-import-api');
```
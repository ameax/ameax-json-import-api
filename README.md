# Ameax JSON Import API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ameax/ameax-json-import-api.svg?style=flat-square)](https://packagist.org/packages/ameax/ameax-json-import-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ameax/ameax-json-import-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ameax/ameax-json-import-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ameax/ameax-json-import-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ameax/ameax-json-import-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ameax/ameax-json-import-api.svg?style=flat-square)](https://packagist.org/packages/ameax/ameax-json-import-api)

A framework-agnostic PHP package for sending data to Ameax via their JSON API. This package provides an easy way to create and send organization and private person data to the Ameax API, with built-in validation according to the JSON schema.

## Installation

You can install the package via composer:

```bash
composer require ameax/ameax-json-import-api
```

## Basic Usage

### Working with Organizations

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Initialize the client
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);

// Create an organization with fluent setters
$organization = $client->createOrganization();

// Add organization details using fluent setters
$organization
    ->setName('ACME Corporation')
    // Set required identifiers
    ->createIdentifiers('CUST12345')
    // Create address (required)
    ->createAddress('12345', 'Berlin', 'DE')
    ->setStreet('Main Street')
    // Set communication details
    ->setEmail('info@acme-corp.com')
    ->setPhone('+49 30 123456789');

// Add a contact
$organization->addContact(
    'John',
    'Doe',
    ['email' => 'john.doe@example.com']
);

// Send to Ameax API
try {
    $response = $organization->sendToAmeax();
    echo "Success!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Working with Private Persons

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Initialize the client
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);

// Create a private person with fluent setters
$privatePerson = $client->createPrivatePerson();

// Add private person details using fluent setters
$privatePerson
    ->setFirstName('John')
    ->setLastName('Doe')
    // Create address (required)
    ->createAddress('12345', 'Berlin', 'DE')
    ->setStreet('Main Street')
    // Set communication details
    ->setEmail('john.doe@example.com')
    ->setPhone('+49 30 123456789');

// Send to Ameax API
try {
    $response = $privatePerson->sendToAmeax();
    echo "Success!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Data Structure

This package follows the Ameax JSON schema structure:

### Organization

The organization structure includes multiple nested objects:

```php
[
    'meta' => [
        'document_type' => 'ameax_organization_account',
        'schema_version' => '1.0'
    ],
    'name' => 'ACME Corporation',
    'additional_name' => 'ACME Corp.',
    'identifiers' => [
        'customer_number' => 'CUST12345',
        'external_id' => 'EXT98765'
    ],
    'address' => [
        'route' => 'Main Street',
        'house_number' => '123',
        'postal_code' => '12345',
        'locality' => 'Berlin',
        'country' => 'DE'
    ],
    'social_media' => [
        'web' => 'https://www.acme-corp.com'
    ],
    'communications' => [
        'phone_number' => '+49 30 123456789',
        'mobile_phone' => '+49 151 123456789',
        'email' => 'info@acme-corp.com',
        'fax' => '+49 30 987654321'
    ],
    'business_information' => [
        'vat_id' => 'DE123456789',
        'iban' => 'DE89370400440532013000'
    ],
    'agent' => [
        'external_id' => 'AGENT123'
    ],
    'custom_data' => [
        'industry' => 'Technology',
        'employees' => 500
    ],
    'contacts' => [/* Array of contact objects */]
]
```

### Private Person

The private person structure is similar to the organization but without contacts:

```php
[
    'meta' => [
        'document_type' => 'ameax_private_person_account',
        'schema_version' => '1.0'
    ],
    'salutation' => 'Mr.',
    'honorifics' => 'Dr.',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'date_of_birth' => '1980-01-01',
    'identifiers' => [
        'customer_number' => 'CUST12345'
    ],
    'address' => [
        'route' => 'Main Street',
        'house_number' => '123',
        'postal_code' => '12345',
        'locality' => 'Berlin',
        'country' => 'DE'
    ],
    'communications' => [
        'phone_number' => '+49 30 123456789',
        'mobile_phone' => '+49 151 123456789',
        'email' => 'john.doe@example.com',
        'fax' => '+49 30 987654321'
    ],
    'agent' => [
        'external_id' => 'AGENT123'
    ],
    'custom_data' => [
        'preferred_language' => 'en',
        'newsletter_subscription' => true
    ]
]
```

### Contact

Contacts have a similar nested structure:

```php
[
    'salutation' => 'Mr.',
    'honorifics' => 'Dr.',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'date_of_birth' => '1980-01-01',
    'identifiers' => [
        'external_id' => 'EMP123'
    ],
    'employment' => [
        'job_title' => 'CEO',
        'department' => 'Management'
    ],
    'communications' => [
        'phone_number' => '+49 30 123456789',
        'mobile_phone' => '+49 151 123456789',
        'email' => 'john.doe@example.com',
        'fax' => '+49 30 987654321'
    ],
    'custom_data' => [
        'language' => 'en',
        'timezone' => 'Europe/Berlin'
    ]
]
```

## Complete Examples

### Organization Example

Here's a comprehensive example using all available features for organizations:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Create the API client
$client = new AmeaxJsonImportApi('your-api-key', 'https://your-database.ameax.de');

try {
    // Create a new organization
    $organization = $client->createOrganization();
    
    // Set basic information
    $organization->setName('ACME Corporation')
                 ->setAdditionalName('ACME Corp.');
    
    // Set identifiers (required)
    $organization->createIdentifiers('CUST12345', 'EXT98765');
    
    // Create address (required)
    $organization->createAddress('12345', 'Berlin', 'DE')
                 ->setRoute('Example Street')
                 ->setHouseNumber('42');
    
    // Set social media
    $organization->setWebsite('https://www.example.com');
    
    // Set communications
    $organization->createCommunications(
        'info@example.com',          // email
        '+49 30 123456789',          // phone
        '+49 151 123456789',         // mobile
        '+49 30 987654321'           // fax
    );
    
    // Set business information
    $organization->createBusinessInformation(
        'DE123456789',               // VAT ID
        'DE89370400440532013000'     // IBAN
    );
    
    // Set agent
    $organization->createAgent('AGENT123');
    
    // Set custom data
    $organization->setCustomData([
        'industry' => 'Technology',
        'employees' => 500
    ]);
    
    // Create and add a contact with full details
    $contact = $client->createContact();
    $contact->setSalutation('Mr.')
            ->setHonorifics('Dr.')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setDateOfBirth('1980-01-01');
    
    // Set contact identifiers
    $contact->createIdentifiers('EMP123');
    
    // Set contact employment
    $contact->createEmployment('CEO', 'Management');
    
    // Set contact communications
    $contact->createCommunications(
        'john.doe@example.com',      // email
        '+49 30 123456789',          // phone
        '+49 151 123456789',         // mobile
        '+49 30 987654321'           // fax
    );
    
    // Add the contact to the organization
    $organization->addContactObject($contact);
    
    // Send to Ameax API
    $response = $organization->sendToAmeax();
    
} catch (ValidationException $e) {
    // Handle validation errors
    print_r($e->getErrors());
} catch (\Exception $e) {
    // Handle other errors
    echo $e->getMessage();
}
```

### Private Person Example

Here's a complete example for creating and sending private person data:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Models\Meta;

// Create the API client
$client = new AmeaxJsonImportApi('your-api-key', 'https://your-database.ameax.de');

try {
    // Create a new private person
    $privatePerson = $client->createPrivatePerson();
    
    // Set basic personal information
    $privatePerson->setSalutation('Mr.')
                  ->setHonorifics('Dr.')
                  ->setFirstName('John')
                  ->setLastName('Doe')
                  ->setDateOfBirth('1980-01-01');
    
    // Set identifiers
    $privatePerson->createIdentifiers('CUST12345');
    
    // Create address (required)
    $privatePerson->createAddress('12345', 'Berlin', 'DE')
                  ->setStreet('Example Street')
                  ->setHouseNumber('42');
    
    // Set communications
    $privatePerson->createCommunications(
        'john.doe@example.com',          // email
        '+49 30 123456789',              // phone
        '+49 151 123456789',             // mobile
        '+49 30 987654321'               // fax
    );
    
    // Set agent
    $privatePerson->createAgent('AGENT123');
    
    // Set custom data
    $privatePerson->setCustomData([
        'preferred_language' => 'en',
        'newsletter_subscription' => true
    ]);
    
    // Send to Ameax API
    $response = $privatePerson->sendToAmeax();
    
} catch (ValidationException $e) {
    // Handle validation errors
    print_r($e->getErrors());
} catch (\Exception $e) {
    // Handle other errors
    echo $e->getMessage();
}
```

### Creating from Existing Data

You can create objects from existing data when you already have all the data structured as an array.

#### Organization from Array

```php
// Complete organization data structure
$data = [
    'meta' => [
        'document_type' => 'ameax_organization_account',
        'schema_version' => '1.0'
    ],
    'name' => 'XYZ Ltd',
    'additional_name' => 'XYZ Limited',
    'identifiers' => [
        'customer_number' => 'CUST54321',
        'external_id' => 'EXT12345'
    ],
    'address' => [
        'route' => 'Main Street',
        'house_number' => '42',
        'postal_code' => '54321',
        'locality' => 'Munich',
        'country' => 'DE',
    ],
    'communications' => [
        'email' => 'info@xyz-ltd.com',
        'phone_number' => '+49 89 123456789'
    ],
    'social_media' => [
        'web' => 'https://www.xyz-ltd.com'
    ],
    'business_information' => [
        'vat_id' => 'DE123456789'
    ],
    'contacts' => [
        [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'communications' => [
                'email' => 'jane.smith@xyz-ltd.com'
            ],
            'employment' => [
                'job_title' => 'CEO'
            ]
        ]
    ],
    'custom_data' => [
        'industry' => 'Manufacturing'
    ]
];

// Create the organization from the array
$organization = $client->organizationFromArray($data);

// Optionally modify some fields
$organization->setEmail('new-email@xyz-ltd.com');

// Send to Ameax API
$response = $organization->sendToAmeax();
```

#### Private Person from Array

```php
// Complete private person data structure
$data = [
    'meta' => [
        'document_type' => 'ameax_private_person_account',
        'schema_version' => '1.0'
    ],
    'salutation' => 'Ms.',
    'firstname' => 'Jane',
    'lastname' => 'Doe',
    'date_of_birth' => '1985-05-15',
    'identifiers' => [
        'customer_number' => 'CUST67890'
    ],
    'address' => [
        'route' => 'Example Road',
        'house_number' => '42',
        'postal_code' => '54321',
        'locality' => 'Munich',
        'country' => 'DE',
    ],
    'communications' => [
        'email' => 'jane.doe@example.com',
        'phone_number' => '+49 89 123456789'
    ],
    'custom_data' => [
        'preferred_language' => 'en',
        'newsletter_subscription' => true
    ]
];

// Create the private person from the array
$privatePerson = $client->privatePersonFromArray($data);

// Optionally modify some fields
$privatePerson->setEmail('new-email@example.com');

// Send to Ameax API
$response = $privatePerson->sendToAmeax();
```

You can also create objects with partial data and disable immediate validation:

```php
// Only provide required fields
$minimalData = [
    'firstname' => 'Jane',
    'lastname' => 'Doe',
    'address' => [
        'postal_code' => '54321',
        'locality' => 'Munich',
        'country' => 'DE',
    ],
];

// Pass false to disable immediate validation
$privatePerson = $client->privatePersonFromArray($minimalData, false);

// Fill in more data before validation
$privatePerson->setEmail('jane.doe@example.com')
             ->setCustomerNumber('CUST67890');

// Manually validate when ready
$privatePerson->validate();

// Send to Ameax API
$response = $privatePerson->sendToAmeax();
```

## JSON Schema Validation

This package validates your data against the Ameax JSON schema before sending it to the API. The validation uses the official Ameax JSON schemas to ensure your data is valid. The package performs validation at multiple levels:

1. **Field-level validation**: Each setter method validates individual fields (e.g., email format, phone number format).
2. **Model-level validation**: The `validate()` method checks required fields and relationships.
3. **Schema-level validation**: Before sending data to the API, it validates against the official Ameax JSON schema.

### Custom Schemas

You can use your own schemas by specifying the schemas path using the fluent setter:

```php
$client = new AmeaxJsonImportApi(
    'your-api-key', 
    'https://your-database.ameax.de'
);

$client->setSchemasPath('/path/to/your/schemas');
```

### Schema Validation

The package provides built-in JSON Schema validation using the `SchemaValidator` class:

```php
use Ameax\AmeaxJsonImportApi\Validation\SchemaValidator;

// Validate data against a schema file
$data = $organization->toArray();
$schemaPath = '/path/to/schemas/ameax_organization_account.json';
SchemaValidator::validate($data, $schemaPath);

// Or validate against a schema string
$schemaJson = file_get_contents($schemaPath);
SchemaValidator::validateWithString($data, $schemaJson);
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

## Versioning

This package follows [Semantic Versioning](https://semver.org/). The version numbers follow the `MAJOR.MINOR.PATCH` format:

- **MAJOR** version increases when incompatible API changes are made
- **MINOR** version increases when functionality is added in a backward-compatible manner
- **PATCH** version increases when backward-compatible bug fixes are implemented

Use the appropriate version constraint in your composer.json:

```json
"require": {
    "ameax/ameax-json-import-api": "^1.0"
}
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
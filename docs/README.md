# Ameax JSON Import API Documentation

Welcome to the documentation for the Ameax JSON Import API package. This package provides an easy way to send data to Ameax using their JSON API.

## Table of Contents

- [Introduction](introduction.md) - Overview of the package
- [Installation](installation.md) - How to install and set up the package
- [Organizations](organizations.md) - Working with organization data
- [API Reference](api-reference.md) - Complete API reference

## Quick Start

Here's a quick example of how to use the package to send organization data to Ameax:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Initialize the client
$client = new AmeaxJsonImportApi('your-api-key', 'https://your-database.ameax.de');

try {
    // Create organization with required fields
    $organization = $client->createOrganization(
        'ACME Corporation',
        '12345',
        'Berlin',
        'DE'
    );
    
    // Add contact person
    $organization = $client->addOrganizationContact(
        $organization,
        'John',
        'Doe',
        ['email' => 'john.doe@acme-corp.com']
    );
    
    // Send to Ameax
    $response = $client->sendOrganization($organization);
    
    echo "Success!";
    
} catch (ValidationException $e) {
    echo "Validation failed: " . implode(", ", $e->getErrors());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Further Reading

For more detailed information, please refer to the specific documentation pages:

- [Installation](installation.md) for setup instructions
- [Organizations](organizations.md) for working with organization data
- [API Reference](api-reference.md) for a complete API reference

## About Ameax JSON Schema

This package is based on the [Ameax JSON Schema](https://github.com/ameax/ameax-json-schema) standard. The schema defines the structure for different types of data that can be sent to Ameax, including organizations, private persons, receipts, and sales.

The current implementation supports:
- Organizations

Future versions will add support for:
- Private persons
- Receipts
- Sales
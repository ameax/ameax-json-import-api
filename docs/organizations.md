# Working with Organizations

This document details how to create, modify, and send organization data using the Ameax JSON Import API package.

## Organization Structure

The Ameax JSON schema for organizations requires the following fields:

- `document_type`: Always set to "ameax_organization_account"
- `schema_version`: The schema version (currently "1.0")
- `name`: The organization name
- `address`: Address object containing:
  - `postal_code`: Postal/ZIP code
  - `locality`: City or town
  - `country`: ISO 3166-1 alpha-2 country code (e.g., "DE" for Germany)

Optional fields include:

- Address details: `street`, `house_number`, etc.
- Communication: `email`, `phone`, `website`, etc.
- Business information: `tax_id`, `vat_id`, etc.
- Contacts: An array of contact persons

## Creating an Organization

Use the `createOrganization` method from the API client to create a new organization:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Initialize the client
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);

// Create an organization with required fields
$organization = $client->createOrganization(
    'ACME Corporation',  // name
    '12345',             // postal_code
    'Berlin',            // locality
    'DE'                 // country
);
```

## Adding Organization Details

After creating an organization, you can use fluent setters to add more details:

```php
// Add address details
$organization
    ->setStreet('Main Street')
    ->setHouseNumber('123');

// Add communication details
$organization
    ->setEmail('info@acme-corp.com')
    ->setPhone('+49 30 123456789')
    ->setWebsite('https://www.acme-corp.com');

// Add business details
$organization
    ->setVatId('DE123456789')
    ->setTaxId('1234567890');
```

## Adding Contact Persons

Use the `addContact` method to add contact persons to an organization:

```php
// Add a primary contact
$organization->addContact(
    'John',              // first_name
    'Doe',               // last_name
    [                    // Optional additional contact data
        'email' => 'john.doe@acme-corp.com',
        'phone' => '+49 30 123456789',
        'job_title' => 'CEO',
        'department' => 'Management'
    ]
);

// Add additional contacts
$organization->addContact(
    'Jane',
    'Smith',
    [
        'email' => 'jane.smith@acme-corp.com',
        'job_title' => 'CTO'
    ]
);
```

## Custom Fields

For any fields not covered by the specific setters, you can use the `setCustomField` method:

```php
$organization->setCustomField('industry', 'Technology');
$organization->setCustomField('employee_count', 250);
$organization->setCustomField('founding_year', 1995);
```

## Creating from Existing Data

If you already have organization data in an array, you can create an organization object from it:

```php
$data = [
    'document_type' => 'ameax_organization_account',
    'schema_version' => '1.0',
    'name' => 'XYZ Ltd',
    'address' => [
        'postal_code' => '54321',
        'locality' => 'Munich',
        'country' => 'DE',
    ],
];

$organization = $client->organizationFromArray($data);
```

## Submitting an Organization

Once you've built your organization object, you can submit it to the Ameax API:

```php
try {
    $response = $organization->submit();
    
    // Handle successful response
    echo "Organization successfully submitted!";
    print_r($response);
} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed:";
    foreach ($e->getErrors() as $error) {
        echo "- " . $error;
    }
} catch (\Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}
```

## Converting to Array

If you need the organization data as an array (e.g., for serialization or debugging):

```php
$organizationArray = $organization->toArray();
```

## Complete Example

Here's a complete example of creating and sending an organization:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Initialize the client
$client = new AmeaxJsonImportApi(
    'your-api-key',
    'https://your-database.ameax.de'
);

try {
    // Create organization
    $organization = $client->createOrganization(
        'ACME Corporation',
        '12345',
        'Berlin',
        'DE'
    );
    
    // Add details
    $organization
        ->setStreet('Main Street')
        ->setHouseNumber('123')
        ->setEmail('info@acme-corp.com')
        ->setPhone('+49 30 123456789')
        ->setWebsite('https://www.acme-corp.com')
        ->setVatId('DE123456789')
        ->setTaxId('1234567890');
    
    // Add contacts
    $organization
        ->addContact(
            'John',
            'Doe',
            [
                'email' => 'john.doe@acme-corp.com',
                'phone' => '+49 30 123456789',
                'job_title' => 'CEO'
            ]
        )
        ->addContact(
            'Jane',
            'Smith',
            [
                'email' => 'jane.smith@acme-corp.com',
                'job_title' => 'CTO'
            ]
        );
    
    // Submit to Ameax
    $response = $organization->submit();
    
    echo "Organization successfully submitted!";
    
} catch (ValidationException $e) {
    echo "Validation failed:";
    foreach ($e->getErrors() as $error) {
        echo "- " . $error;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```
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

Use the `createOrganization` method to create a new organization with the required fields:

```php
$organization = $client->createOrganization(
    'ACME Corporation',  // name
    '12345',             // postal_code
    'Berlin',            // locality
    'DE'                 // country (ISO 3166-1 alpha-2)
);
```

This creates a basic organization object with the required fields. You can then add more details:

```php
$organization['street'] = 'Main Street';
$organization['house_number'] = '123';
$organization['email'] = 'info@acme-corp.com';
$organization['phone'] = '+49 30 123456789';
$organization['website'] = 'https://www.acme-corp.com';
```

## Adding Contact Persons

Use the `addOrganizationContact` method to add contact persons to an organization:

```php
$organization = $client->addOrganizationContact(
    $organization,       // The organization array
    'John',              // first_name
    'Doe',               // last_name
    [                    // Optional additional contact data
        'email' => 'john.doe@acme-corp.com',
        'phone' => '+49 30 123456789',
        'job_title' => 'CEO',
        'department' => 'Management'
    ]
);
```

You can add multiple contacts by calling this method multiple times.

## Sending an Organization to Ameax

Once you've created and populated your organization data, use the `sendOrganization` method to send it to Ameax:

```php
try {
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

## Complete Example

Here's a complete example of creating and sending an organization:

```php
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Initialize the client
$client = new AmeaxJsonImportApi('your-api-key', 'https://your-database.ameax.de');

try {
    // Create organization
    $organization = $client->createOrganization(
        'ACME Corporation',
        '12345',
        'Berlin',
        'DE'
    );
    
    // Add details
    $organization['street'] = 'Main Street';
    $organization['house_number'] = '123';
    $organization['email'] = 'info@acme-corp.com';
    
    // Add contact
    $organization = $client->addOrganizationContact(
        $organization,
        'John',
        'Doe',
        [
            'email' => 'john.doe@acme-corp.com',
            'phone' => '+49 30 123456789',
            'job_title' => 'CEO'
        ]
    );
    
    // Add another contact
    $organization = $client->addOrganizationContact(
        $organization,
        'Jane',
        'Smith',
        [
            'email' => 'jane.smith@acme-corp.com',
            'phone' => '+49 30 987654321',
            'job_title' => 'CTO'
        ]
    );
    
    // Send to Ameax
    $response = $client->sendOrganization($organization);
    
    echo "Organization successfully sent to Ameax!\n";
    echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
} catch (ValidationException $e) {
    echo "JSON validation failed:\n";
    foreach ($e->getErrors() as $error) {
        echo "- {$error}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Schema Validation

The package validates your organization data against the Ameax JSON schema before sending it. If validation fails, a `ValidationException` is thrown with detailed error messages.

You can handle validation errors like this:

```php
try {
    $response = $client->sendOrganization($organization);
} catch (ValidationException $e) {
    // Get all validation errors
    $errors = $e->getErrors();
    
    // Display errors
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}
```
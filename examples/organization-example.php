<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Initialize API client with API key and host URL
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$client = new AmeaxJsonImportApi($apiKey, $host);

try {
    // Method 1: Create an organization with fluent setters
    $organization = $client->createOrganization(
        'ACME Corporation',  // name
        '12345',             // postal_code
        '12345',             // locality
        'DE'                 // country
    );
    
    // Add more organization data using fluent setters
    $organization
        ->setStreet('Main Street')
        ->setHouseNumber('123')
        ->setEmail('info@acme-corp.com')
        ->setPhone('+49 30 123456789')
        ->setWebsite('https://www.acme-corp.com')
        ->setVatId('DE123456789')
        ->setTaxId('1234567890');
    
    // Add a contact person
    $organization->addContact(
        'John',              // first_name
        'Doe',               // last_name
        [
            'email' => 'john.doe@acme-corp.com',
            'phone' => '+49 30 123456789',
            'job_title' => 'CEO',
            'department' => 'Management'
        ]
    );
    
    // Add another contact with more direct validation
    $organization->addContact('Jane', 'Smith', [
        'email' => 'jane.smith@acme-corp.com',
        'job_title' => 'CTO'
    ]);
    
    // Set some custom fields
    $organization->setCustomField('industry', 'Technology');
    $organization->setCustomField('founded_year', 1995);
    
    // Set multiple custom fields at once
    $organization->setCustomData([
        'employees_count' => 250,
        'annual_revenue' => '10M-50M',
        'public_company' => false
    ]);
    
    // Method 2: Create from an existing array
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
    
    $organization2 = $client->organizationFromArray($data);
    $organization2->setEmail('info@xyz-ltd.com');
    
    // Validate before sending
    if ($organization->validate()) {
        echo "Organization data is valid!\n";
    }
    
    // Send the first organization to Ameax
    $response = $organization->sendToAmeax();
    
    echo "Organization successfully sent to Ameax!\n";
    echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
} catch (ValidationException $e) {
    echo "Validation failed:\n";
    foreach ($e->getErrors() as $error) {
        echo "- {$error}\n";
    }
} catch (\InvalidArgumentException $e) {
    echo "Invalid argument: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
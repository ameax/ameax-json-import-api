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
        'Berlin',            // locality
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
    
    // Add another contact
    $organization->addContact('Jane', 'Smith', [
        'email' => 'jane.smith@acme-corp.com',
        'job_title' => 'CTO'
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
    
    // Submit the first organization to Ameax
    $response = $organization->submit();
    
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
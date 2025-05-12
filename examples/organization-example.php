<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

// Initialize API client with API key and host URL
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de'; // Or http://your-database.ameax.localhost for local development
$client = new AmeaxJsonImportApi($apiKey, $host);

try {
    // Create a new organization with required fields
    $organization = $client->createOrganization(
        'ACME Corporation',
        '12345',
        'Berlin',
        'DE'
    );
    
    // Add additional organization data
    $organization['street'] = 'Main Street';
    $organization['house_number'] = '123';
    $organization['email'] = 'info@acme-corp.com';
    $organization['phone'] = '+49 30 123456789';
    $organization['website'] = 'https://www.acme-corp.com';
    
    // Add contact person
    $organization = $client->addOrganizationContact(
        $organization,
        'John',
        'Doe',
        [
            'email' => 'john.doe@acme-corp.com',
            'phone' => '+49 30 123456789',
            'job_title' => 'CEO',
            'department' => 'Management'
        ]
    );
    
    // Send the organization data to Ameax
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
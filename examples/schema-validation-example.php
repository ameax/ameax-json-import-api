<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\SchemaValidator;

// Create the API client
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$client = new AmeaxJsonImportApi($apiKey, $host);

// Set path to schemas directory
$client->setSchemasPath(__DIR__ . '/../resources/schemas');

// Create a new organization
$organization = $client->createOrganization();
$organization->setName('ACME Corporation')
    ->createAddress('12345', 'Berlin', 'DE')
    ->setStreet('Example Street')
    ->setHouseNumber('42')
    ->setEmail('info@example.com')
    ->setPhone('+49 30 1234567')
    ->setWebsite('https://www.example.com')
    ->setVatId('DE123456789')
    ->setTaxId('12345/67890');

// Add a contact to the organization
$organization->addContact('John', 'Doe', [
    'email' => 'john.doe@example.com',
    'phone' => '+49 30 1234567',
    'job_title' => 'CEO',
    'department' => 'Management'
]);

try {
    // Validate the organization
    $organization->validate();
    echo "Organization is valid!\n";
    
    // Get the organization data as an array
    $data = $organization->toArray();
    
    // Validate against the JSON schema
    $schemaPath = __DIR__ . '/../resources/schemas/ameax_organization_account.json';
    SchemaValidator::validate($data, $schemaPath);
    echo "Organization data is valid according to the JSON schema!\n";
    
    // Print the organization data in pretty JSON format
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    
    // Send to Ameax API (commented out to prevent actual API calls)
    // $response = $organization->sendToAmeax();
    // echo "Organization sent successfully!\n";
    // echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
} catch (ValidationException $e) {
    echo "Validation error:\n";
    foreach ($e->getErrors() as $error) {
        echo " - {$error}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
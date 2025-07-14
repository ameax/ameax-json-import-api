<?php

require_once __DIR__.'/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Initialize API client with API key and host URL
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$client = new AmeaxJsonImportApi($apiKey, $host);

// Optionally set a custom path for JSON schema files
// $client->setSchemasPath('/path/to/custom/schemas');

try {
    // Method 1: Create an organization with fluent setters
    $organization = $client->createOrganization();

    // Set required fields
    $organization->setName('ACME Corporation');

    // Create and set address
    $organization->createAddress('12345', 'Berlin', 'DE');

    // Set more optional fields using fluent setters
    $organization
        ->setStreet('Main Street')
        ->setHouseNumber('123')
        ->setEmail('info@acme-corp.com')
        ->setPhone('+49 30 123456789')
        ->setPhoneTwo('+49 30 123456790')  // Second phone number
        ->setWebsite('https://www.acme-corp.com')
        ->setVatId('DE123456789')
        ->setTaxId('1234567890');

    // Add a contact person - method 1
    $organization->addContact(
        'John',              // first_name
        'Doe',               // last_name
        [
            'email' => 'john.doe@acme-corp.com',
            'phone' => '+49 30 123456789',
            'phone_number2' => '+49 30 123456791',  // Second phone number for contact
            'job_title' => 'CEO',
            'department' => 'Management',
        ]
    );

    // Add a contact person - method 2
    $contact = $client->createContact();
    $contact
        ->setFirstName('Jane')
        ->setLastName('Smith')
        ->setEmail('jane.smith@acme-corp.com')
        ->setPhone('+49 30 123456792')
        ->setPhoneTwo('+49 30 123456793')  // Demonstrate second phone number setter
        ->setJobTitle('CTO');

    $organization->addContactObject($contact);

    // Set some custom fields
    $organization->setCustomField('industry', 'Technology');
    $organization->setCustomField('founded_year', 1995);

    // Set multiple custom fields at once
    $organization->setCustomData([
        'employees_count' => 250,
        'annual_revenue' => '10M-50M',
        'public_company' => false,
    ]);

    // Method 2: Create from an existing array
    $data = [
        'name' => 'XYZ Ltd',
        'address' => [
            'postal_code' => '54321',
            'locality' => 'Munich',
            'country' => 'DE',
        ],
    ];

    $organization2 = $client->organizationFromArray($data);
    $organization2->setEmail('info@xyz-ltd.com');

    // Organization data is ready to send
    echo "Organization data is ready to send!\n";

    // Send the first organization to Ameax
    $response = $organization->sendToAmeax();

    echo "Organization successfully sent to Ameax!\n";
    echo 'Response: '.json_encode($response, JSON_PRETTY_PRINT)."\n";

} catch (\InvalidArgumentException $e) {
    echo 'Invalid argument: '.$e->getMessage()."\n";
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

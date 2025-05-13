<?php

require_once __DIR__.'/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Create the API client
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$client = new AmeaxJsonImportApi($apiKey, $host);

// Set path to schemas directory if you have custom schemas
$client->setSchemasPath(__DIR__.'/../resources/schemas');

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
        'founded' => 1995,
        'employees' => 500,
        'is_active' => true,
    ]);

    // Add a contact using the new structure
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

    // Set contact custom data
    $contact->setCustomData([
        'language' => 'en',
        'timezone' => 'Europe/Berlin',
    ]);

    // Add the contact to the organization
    $organization->addContactObject($contact);

    // Add another contact with simpler method
    $organization->addContact(
        'Jane',
        'Smith',
        [
            'salutation' => 'Ms.',
            'email' => 'jane.smith@example.com',
            'phone' => '+49 30 123456780',
            'job_title' => 'CTO',
            'department' => 'Technology',
        ]
    );

    // Organization is ready to be sent
    echo "Organization is ready to be sent.\n";

    // Convert to array and display
    $data = $organization->toArray();
    echo "Organization data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo "\n\n";

    // This is commented out to avoid actual API calls during testing
    // $response = $organization->sendToAmeax();
    // echo "API Response:\n";
    // echo json_encode($response, JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

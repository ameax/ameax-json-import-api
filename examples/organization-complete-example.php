<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Create the API client
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$client = new AmeaxJsonImportApi($apiKey, $host);

// Set path to custom schemas if needed
// $client->setSchemasPath('/path/to/your/schemas');

try {
    // Create a new organization
    $organization = $client->createOrganization();
    
    // Set required organization data using fluent setters
    $organization->setName('ACME Corporation')
        ->createAddress('12345', 'Berlin', 'DE');
    
    // Add optional additional organization data using fluent setters
    $organization->setStreet('Main Street')
        ->setHouseNumber('123')
        ->setEmail('info@acme-corp.com')
        ->setPhone('+49 30 123456789')
        ->setWebsite('https://www.acme-corp.com')
        ->setVatId('DE123456789')
        ->setTaxId('12345/67890');
    
    // Add a contact person to the organization using array syntax
    $organization->addContact(
        'John',              // first_name
        'Doe',               // last_name
        [
            'email' => 'john.doe@acme-corp.com',
            'phone' => '+49 30 123456789',
            'mobile' => '+49 151 123456789',
            'job_title' => 'CEO',
            'department' => 'Management'
        ]
    );
    
    // Or create a contact directly and add it to the organization
    $contact = $client->createContact();
    $contact->setFirstName('Jane')
            ->setLastName('Smith')
            ->setEmail('jane.smith@acme-corp.com')
            ->setPhone('+49 30 987654321')
            ->setMobile('+49 151 987654321')
            ->setJobTitle('CTO')
            ->setDepartment('Technology');
            
    $organization->addContactObject($contact);
    
    // Get data from the organization if needed
    $orgName = $organization->getName();
    $orgEmail = $organization->getEmail();
    $orgContacts = $organization->getContacts();
    
    // Set custom fields if needed
    $organization->setCustomField('industry', 'Technology');
    $organization->setCustomField('employee_count', 500);
    $organization->setCustomField('founded_year', 1995);
    
    // Or set multiple custom fields at once
    $organization->setCustomData([
        'is_customer' => true,
        'customer_since' => '2023-01-01',
        'customer_tier' => 'premium'
    ]);
    
    // Organization is ready to be sent
    echo "Organization is ready to be sent!\n";
    
    // Convert to array for inspection
    $orgArray = $organization->toArray();
    echo json_encode($orgArray, JSON_PRETTY_PRINT) . "\n";
    
    // Send the organization to Ameax API (commented out to prevent actual API calls)
    // $response = $organization->sendToAmeax();
    // echo "Organization sent successfully!\n";
    // echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
} catch (\Exception $e) {
    // Handle errors
    echo "Error: " . $e->getMessage() . "\n";
}
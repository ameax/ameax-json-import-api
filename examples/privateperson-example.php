<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Models\Meta;

// Create an API client instance
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$api = new AmeaxJsonImportApi($apiKey, $host);

// Option 1: Create a private person step by step using the fluent interface
try {
    $privatePerson = $api->createPrivatePerson()
        ->setFirstName('John')
        ->setLastName('Doe')
        ->setDateOfBirth('1980-01-01')
        ->setSalutation('Mr.')
        ->createAddress('12345', 'Berlin', 'DE')
        ->setStreet('Example Street')
        ->setHouseNumber('123')
        ->setEmail('john.doe@example.com')
        ->setPhone('+49 123 4567890')
        ->setCustomerNumber('CUST12345');
    
    // You would typically call sendToAmeax() to send the data to Ameax
    // $response = $privatePerson->sendToAmeax();
    // var_dump($response);
    
    // For this example, just print the data
    echo json_encode($privatePerson->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

// Option 2: Create a private person from an array of data
try {
    $data = [
        'meta' => [
            'document_type' => Meta::DOCUMENT_TYPE_PRIVATE_PERSON,
            'schema_version' => '1.0'
        ],
        'firstname' => 'Jane',
        'lastname' => 'Doe',
        'salutation' => 'Ms.',
        'date_of_birth' => '1985-05-15',
        'address' => [
            'route' => 'Sample Road',
            'house_number' => '456',
            'postal_code' => '54321',
            'locality' => 'Munich',
            'country' => 'DE'
        ],
        'communications' => [
            'email' => 'jane.doe@example.com',
            'phone_number' => '+49 987 6543210',
            'mobile_phone' => '+49 123 9876543'
        ],
        'identifiers' => [
            'customer_number' => 'CUST67890'
        ],
        'custom_data' => [
            'preferred_language' => 'en',
            'newsletter_subscription' => true
        ]
    ];
    
    $privatePerson = $api->privatePersonFromArray($data);
    
    // You would typically call sendToAmeax() to send the data to Ameax
    // $response = $privatePerson->sendToAmeax();
    // var_dump($response);
    
    // For this example, just print the data
    echo json_encode($privatePerson->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

// Example of adding data to an existing private person
try {
    $privatePerson = $api->createPrivatePerson()
        ->setFirstName('Alice')
        ->setLastName('Smith');
    
    // Add required address information
    $privatePerson->createAddress('67890', 'Hamburg', 'DE');
    
    echo "Private person created with required address." . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
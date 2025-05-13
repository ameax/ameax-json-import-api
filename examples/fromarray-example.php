<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

// Create the API client
$apiKey = 'your-api-key';
$host = 'https://your-database.ameax.de';
$client = new AmeaxJsonImportApi($apiKey, $host);

// Example 1: Create an organization with complete data
echo "Example 1: Complete data structure\n";
echo "=================================\n";

try {
    // Complete data structure
    $completeData = [
        'meta' => [
            'document_type' => 'ameax_organization_account',
            'schema_version' => '1.0'
        ],
        'name' => 'XYZ Ltd',
        'additional_name' => 'XYZ Limited',
        'identifiers' => [
            'customer_number' => 'CUST54321',
            'external_id' => 'EXT12345'
        ],
        'address' => [
            'route' => 'Main Street',
            'house_number' => '42',
            'postal_code' => '54321',
            'locality' => 'Munich',
            'country' => 'DE',
        ],
        'communications' => [
            'email' => 'info@xyz-ltd.com',
            'phone_number' => '+49 89 123456789',
            'mobile_phone' => '+49 151 123456789',
            'fax' => '+49 89 987654321'
        ],
        'social_media' => [
            'web' => 'https://www.xyz-ltd.com'
        ],
        'business_information' => [
            'vat_id' => 'DE123456789',
            'iban' => 'DE89370400440532013000'
        ],
        'agent' => [
            'external_id' => 'AGENT456'
        ],
        'contacts' => [
            [
                'salutation' => 'Ms.',
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'date_of_birth' => '1985-05-15',
                'identifiers' => [
                    'external_id' => 'EMP001'
                ],
                'employment' => [
                    'job_title' => 'CEO',
                    'department' => 'Management'
                ],
                'communications' => [
                    'email' => 'jane.smith@xyz-ltd.com',
                    'phone_number' => '+49 89 123456780',
                    'mobile_phone' => '+49 151 987654321'
                ],
                'custom_data' => [
                    'languages' => ['en', 'de']
                ]
            ],
            [
                'salutation' => 'Mr.',
                'firstname' => 'John',
                'lastname' => 'Doe',
                'employment' => [
                    'job_title' => 'CTO',
                    'department' => 'Technology'
                ],
                'communications' => [
                    'email' => 'john.doe@xyz-ltd.com'
                ]
            ]
        ],
        'custom_data' => [
            'industry' => 'Manufacturing',
            'employees' => 250,
            'founded' => 1995,
            'is_active' => true
        ]
    ];
    
    // Create the organization from the complete data array
    $organization = $client->organizationFromArray($completeData);
    
    // Organization is created successfully
    echo "Organization created successfully!\n";
    
    // You can still make changes if needed
    $organization->setEmail('new-email@xyz-ltd.com');
    
    // Print the organization data in pretty JSON format
    echo "Organization data:\n";
    echo json_encode($organization->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo "\n\n";
    
    // This is commented out to avoid actual API calls during testing
    // $response = $organization->sendToAmeax();
    // echo "API Response:\n";
    // echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 2: Create an organization with minimal data and manual validation
echo "\nExample 2: Minimal data with deferred validation\n";
echo "==========================================\n";

try {
    // Minimal required data
    $minimalData = [
        'name' => 'ABC GmbH',
        'identifiers' => [
            'customer_number' => 'CUST98765'
        ],
        'address' => [
            'postal_code' => '10115',
            'locality' => 'Berlin',
            'country' => 'DE',
        ],
    ];
    
    // Create the organization
    $organization = $client->organizationFromArray($minimalData);
    echo "Organization created with minimal data\n";
    
    // Fill in more data
    $organization->setEmail('info@abc-gmbh.de')
                ->setWebsite('https://www.abc-gmbh.de')
                ->setPhone('+49 30 123456789');
    
    // Add a contact
    $organization->addContact('Max', 'Mustermann', [
        'email' => 'max.mustermann@abc-gmbh.de',
        'phone' => '+49 30 123456780'
    ]);
    
    echo "Organization updated with additional data\n";
    
    // Print the organization data in pretty JSON format
    echo "Organization data:\n";
    echo json_encode($organization->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo "\n\n";
    
    // This is commented out to avoid actual API calls during testing
    // $response = $organization->sendToAmeax();
    // echo "API Response:\n";
    // echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
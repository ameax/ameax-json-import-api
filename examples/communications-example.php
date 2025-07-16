<?php

require_once __DIR__.'/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\Models\PrivatePerson;

// Example 1: Organization with all communication fields
$organization = new Organization;
$organization->setName('Tech Corp International')
    ->setCustomerNumber('TECH-001')
    ->createAddress('10001', 'New York', 'US');

// Set all available communication fields
$organization->setPhone('+1 212-555-0100')          // Main phone
    ->setPhoneNumberTwo('+1 212-555-0101')          // Secondary phone (still supported)
    ->setMobilePhone('+1 917-555-0200')             // Mobile phone (new)
    ->setEmail('contact@techcorp.com')
    ->setFax('+1 212-555-0199');                    // Fax (new)

echo "Organization with all communication fields:\n";
echo json_encode($organization->toArray(), JSON_PRETTY_PRINT)."\n\n";

// Example 2: Private Person with all communication fields
$privatePerson = new PrivatePerson;
$privatePerson->setSalutation('Ms.')
    ->setFirstname('Jane')
    ->setLastname('Smith')
    ->createAddress('90210', 'Beverly Hills', 'US');

// Set communication fields
$privatePerson->setPhone('+1 310-555-0100')         // Home phone
    ->setPhoneNumberTwo('+1 310-555-0101')          // Work phone (still supported)
    ->setMobilePhone('+1 424-555-0200')             // Mobile phone (new)
    ->setEmail('jane.smith@email.com')
    ->setFax('+1 310-555-0199');                    // Personal fax (new)

echo "Private Person with all communication fields:\n";
echo json_encode($privatePerson->toArray(), JSON_PRETTY_PRINT)."\n\n";

// Example 3: Creating data from array with all communication fields
$dataWithAllFields = [
    'meta' => [
        'document_type' => 'ameax_organization_account',
        'schema_version' => '1.0',
        'import_mode' => 'create_or_update',
    ],
    'name' => 'Complete Communications Example',
    'identifiers' => [
        'customer_number' => 'COMM-003',
    ],
    'communications' => [
        'phone_number' => '+1 415-555-0100',
        'phone_number2' => '+1 415-555-0101',     // Secondary phone
        'mobile_phone' => '+1 650-555-0200',      // Mobile (new)
        'email' => 'info@complete-comm.com',
        'fax' => '+1 415-555-0199',                // Fax (new)
    ],
];

$organizationFromArray = Organization::fromArray($dataWithAllFields);

echo "Organization created from array with all communication fields:\n";
echo json_encode($organizationFromArray->toArray(), JSON_PRETTY_PRINT)."\n";

<?php

require_once __DIR__.'/../vendor/autoload.php';

use Ameax\AmeaxJsonImportApi\Models\Meta;
use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\Models\Sale;

// Example 1: Create-only mode - will only create new records
$organizationCreateOnly = new Organization;
$organizationCreateOnly->setName('New Company Inc.')
    ->setCustomerNumber('CUST-12345')
    ->createAddress('10001', 'New York', 'US');

// Set import mode to create_only
$organizationCreateOnly->getMeta()->setImportMode(Meta::IMPORT_MODE_CREATE_ONLY);

echo "Organization with create_only mode:\n";
echo json_encode($organizationCreateOnly->toArray(), JSON_PRETTY_PRINT)."\n\n";

// Example 2: Update-only mode - will only update existing records
$organizationUpdateOnly = new Organization;
$organizationUpdateOnly->setName('Updated Company Inc.')
    ->setCustomerNumber('CUST-67890');

// Set import mode to update_only
$organizationUpdateOnly->getMeta()->setImportMode(Meta::IMPORT_MODE_UPDATE_ONLY);

echo "Organization with update_only mode:\n";
echo json_encode($organizationUpdateOnly->toArray(), JSON_PRETTY_PRINT)."\n\n";

// Example 3: Default mode (create_or_update) - will create or update as needed
$organizationDefault = new Organization;
$organizationDefault->setName('Flexible Company Inc.')
    ->setCustomerNumber('CUST-11111');

// Import mode defaults to create_or_update, but we can set it explicitly
$organizationDefault->getMeta()->setImportMode(Meta::IMPORT_MODE_CREATE_OR_UPDATE);

echo "Organization with create_or_update mode (default):\n";
echo json_encode($organizationDefault->toArray(), JSON_PRETTY_PRINT)."\n\n";

// Example 4: Sale with import mode
$sale = new Sale;
$sale->setSubject('New Deal')
    ->setSaleStatus('active')
    ->setSellingStatus('qualification')
    ->setUserExternalId('USER-123')
    ->setDate('2025-07-16')
    ->setAmount(50000.00)
    ->setProbability(75);

// Set customer by external ID
$sale->setCustomerByExternalId('CUST-12345');

// Set import mode for the sale
$sale->getMeta()->setImportMode(Meta::IMPORT_MODE_CREATE_ONLY);

echo "Sale with create_only mode:\n";
echo json_encode($sale->toArray(), JSON_PRETTY_PRINT)."\n";

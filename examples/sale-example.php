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
    // Create a new sale with fluent setters
    $sale = $client->createSale();

    // Set required fields
    $sale->setExternalId('S001')
         ->setSubject('New Software Implementation')
         ->setSaleStatus('active')
         ->setSellingStatus('qualification')
         ->setUserExternalId('JD123')
         ->setDate('2025-06-01')
         ->setAmount(15000.00)
         ->setProbability(70)
         ->setCloseDate('2025-09-30');

    // Set customer information - using external_id
    $sale->setCustomerExternalId('ORG123');
    // Alternatively: $sale->setCustomerNumber('12345');

    // Optional description
    $sale->setDescription('Implementation of our enterprise software package for client XYZ');

    // Add ratings (all required)
    $sale->setRating('relationship', 5, 'known')
         ->setRating('proposition', 6, 'known')
         ->setRating('trust', 5, 'assumed')
         ->setRating('competition', 3, 'known')
         ->setRating('need_for_action', 4, 'assumed')
         ->setRating('buying_process', 5, 'known')
         ->setRating('price', 4, 'assumed');

    // Set custom data fields
    $sale->setCustomData([
        'opportunity_source' => 'referral',
        'industry_sector' => 'healthcare',
        'expected_roi' => '25%'
    ]);

    // Method 2: Create from an existing array
    $data = [
        'identifiers' => [
            'external_id' => 'S002'
        ],
        'customer' => [
            'external_id' => 'ORG456'
        ],
        'subject' => 'Hardware Upgrade Project',
        'sale_status' => 'active',
        'selling_status' => 'proposal',
        'user_external_id' => 'JS456',
        'date' => '2025-07-15',
        'amount' => 25000.00,
        'probability' => 85,
        'close_date' => '2025-10-31'
    ];

    $sale2 = $client->saleFromArray($data);
    
    // Add missing required ratings to the second sale
    $sale2->setRating('relationship', 6, 'known')
          ->setRating('proposition', 5, 'known')
          ->setRating('trust', 6, 'known')
          ->setRating('competition', 4, 'assumed')
          ->setRating('need_for_action', 5, 'known')
          ->setRating('buying_process', 4, 'assumed')
          ->setRating('price', 5, 'known');

    // Sale data is ready to send
    echo "Sale data is ready to send!\n";

    // Send the first sale to Ameax
    $response = $sale->sendToAmeax();

    echo "Sale successfully sent to Ameax!\n";
    echo 'Response: '.json_encode($response, JSON_PRETTY_PRINT)."\n";

} catch (\InvalidArgumentException $e) {
    echo 'Invalid argument: '.$e->getMessage()."\n";
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
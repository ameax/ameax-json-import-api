<?php

use Ameax\AmeaxJsonImportApi\Models\Receipt;
use Ameax\AmeaxJsonImportApi\Models\LineItem;

it('can set sale external id', function () {
    $receipt = new Receipt();

    $receipt->setSaleExternalId('SALE-001');
    expect($receipt->getSaleExternalId())->toBe('SALE-001');
});

it('can remove sale external id', function () {
    $receipt = new Receipt();

    $receipt->setSaleExternalId('SALE-001');
    expect($receipt->getSaleExternalId())->toBe('SALE-001');

    $receipt->setSaleExternalId(null);
    expect($receipt->getSaleExternalId())->toBeNull();
});

it('can create receipt from array with sale external id', function () {
    $data = [
        'type' => Receipt::TYPE_INVOICE,
        'identifiers' => [
            'receipt_number' => 'INV-2024-001',
        ],
        'date' => '2024-01-01',
        'customer_number' => 'CUST-123',
        'status' => Receipt::STATUS_COMPLETED,
        'tax_mode' => Receipt::TAX_MODE_NET,
        'tax_type' => Receipt::TAX_TYPE_REGULAR,
        'sale_external_id' => 'SALE-001',
        'line_items' => [
            [
                'description' => 'Product A',
                'quantity' => 1,
                'price' => 100.00,
                'tax_rate' => 19,
                'tax_type' => 'regular',
            ],
        ],
    ];

    $receipt = Receipt::fromArray($data);

    expect($receipt->getSaleExternalId())->toBe('SALE-001');
});

it('sale external id is optional', function () {
    $receipt = new Receipt();

    $receipt->setType(Receipt::TYPE_INVOICE)
        ->setReceiptNumber('INV-2024-001')
        ->setDate('2024-01-01')
        ->setCustomerNumber('CUST-123')
        ->setStatus(Receipt::STATUS_COMPLETED)
        ->setTaxMode(Receipt::TAX_MODE_NET)
        ->setTaxType(Receipt::TAX_TYPE_REGULAR)
        ->addLineItem(
            (new LineItem())
                ->setDescription('Product A')
                ->setQuantity(1)
                ->setPrice(100.00)
                ->setTaxRate(19)
                ->setTaxType('regular')
        );

    // Not setting sale_external_id
    $data = $receipt->toArray();

    expect($data)->not->toHaveKey('sale_external_id');
});

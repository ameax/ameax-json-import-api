<?php

use Ameax\AmeaxJsonImportApi\Models\LineItem;
use Ameax\AmeaxJsonImportApi\Models\Receipt;

it('can set and get uom on line item', function () {
    $lineItem = new LineItem;

    $lineItem->setUom('pcs');
    expect($lineItem->getUom())->toBe('pcs');
});

it('can remove uom from line item', function () {
    $lineItem = new LineItem;

    $lineItem->setUom('kg');
    expect($lineItem->getUom())->toBe('kg');

    $lineItem->setUom(null);
    expect($lineItem->getUom())->toBeNull();
});

it('can create line item from array with uom', function () {
    $data = [
        'description' => 'Product A',
        'quantity' => 5,
        'price' => 20.00,
        'tax_rate' => 19,
        'tax_type' => 'regular',
        'uom' => 'boxes',
    ];

    $lineItem = LineItem::fromArray($data);

    expect($lineItem->getUom())->toBe('boxes');
    expect($lineItem->getDescription())->toBe('Product A');
    expect($lineItem->getQuantity())->toBe(5.0);
});

it('uom is optional in line item', function () {
    $lineItem = new LineItem;

    $lineItem->setDescription('Product B')
        ->setQuantity(2)
        ->setPrice(50.00)
        ->setTaxRate(19)
        ->setTaxType('regular');

    // Not setting uom
    $data = $lineItem->toArray();

    expect($data)->not->toHaveKey('uom');
    expect($lineItem->getUom())->toBeNull();
});

it('includes uom in array output when set', function () {
    $lineItem = new LineItem;

    $lineItem->setDescription('Product C')
        ->setQuantity(1)
        ->setPrice(100.00)
        ->setTaxRate(19)
        ->setTaxType('regular')
        ->setUom('liters');

    $data = $lineItem->toArray();

    expect($data)->toHaveKey('uom');
    expect($data['uom'])->toBe('liters');
});

it('can create receipt with line items that have uom', function () {
    $receipt = new Receipt;

    $receipt->setType(Receipt::TYPE_INVOICE)
        ->setReceiptNumber('INV-2024-001')
        ->setDate('2024-01-01')
        ->setCustomerNumber('CUST-123')
        ->setStatus(Receipt::STATUS_COMPLETED)
        ->setTaxMode(Receipt::TAX_MODE_NET)
        ->setTaxType(Receipt::TAX_TYPE_REGULAR)
        ->addLineItem(
            (new LineItem)
                ->setDescription('Product with UOM')
                ->setQuantity(3)
                ->setPrice(25.00)
                ->setTaxRate(19)
                ->setTaxType('regular')
                ->setUom('meters')
        );

    $data = $receipt->toArray();

    expect($data['line_items'])->toHaveCount(1);
    expect($data['line_items'][0]['uom'])->toBe('meters');
    expect($data['line_items'][0]['description'])->toBe('Product with UOM');
});
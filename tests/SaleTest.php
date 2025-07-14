<?php

use Ameax\AmeaxJsonImportApi\Models\Sale;

it('can set new sale status values', function () {
    $sale = new Sale();

    // Test new status values
    $sale->setSaleStatus(Sale::STATUS_TERMINATED);
    expect($sale->getSaleStatus())->toBe('terminated');

    $sale->setSaleStatus(Sale::STATUS_LOST);
    expect($sale->getSaleStatus())->toBe('lost');

    $sale->setSaleStatus(Sale::STATUS_WON);
    expect($sale->getSaleStatus())->toBe('won');
});

it('validates sale status values', function () {
    $sale = new Sale();

    expect(function () use ($sale) {
        $sale->setSaleStatus('invalid_status');
    })->toThrow(InvalidArgumentException::class);
});

it('can add remind actions', function () {
    $sale = new Sale();

    $sale->addRemindAction('2024-12-25T10:00:00', 'Follow up on proposal');

    $actions = $sale->getCreateActions();
    expect($actions)->toHaveCount(1);
    expect($actions[0])->toBe([
        'type' => 'remind',
        'remind_date' => '2024-12-25T10:00:00',
        'subject' => 'Follow up on proposal',
    ]);
});

it('can add remind action with DateTime object', function () {
    $sale = new Sale();

    $date = new DateTime('2024-12-25 10:00:00');
    $sale->addRemindAction($date, 'Follow up on proposal');

    $actions = $sale->getCreateActions();
    expect($actions)->toHaveCount(1);
    expect($actions[0]['remind_date'])->toBe('2024-12-25T10:00:00');
});

it('can set multiple create actions', function () {
    $sale = new Sale();

    $actions = [
        [
            'type' => 'remind',
            'remind_date' => '2024-12-25T10:00:00',
            'subject' => 'First reminder',
        ],
        [
            'type' => 'remind',
            'remind_date' => '2024-12-26T10:00:00',
            'subject' => 'Second reminder',
        ],
    ];

    $sale->setCreateActions($actions);
    expect($sale->getCreateActions())->toBe($actions);
});

it('validates remind actions have required fields', function () {
    $sale = new Sale();

    expect(function () use ($sale) {
        $sale->setCreateActions([
            [
                'type' => 'remind',
                // Missing remind_date and subject
            ],
        ]);
    })->toThrow(InvalidArgumentException::class);
});

it('validates actions have type field', function () {
    $sale = new Sale();

    expect(function () use ($sale) {
        $sale->setCreateActions([
            [
                'remind_date' => '2024-12-25T10:00:00',
                'subject' => 'Reminder',
                // Missing type
            ],
        ]);
    })->toThrow(InvalidArgumentException::class);
});

it('close date is optional', function () {
    $sale = new Sale();

    $sale->setExternalId('SALE-001')
        ->setCustomerNumber('CUST-123')
        ->setSubject('Test Sale')
        ->setSaleStatus(Sale::STATUS_ACTIVE)
        ->setSellingStatus(Sale::SELLING_STATUS_PROPOSAL)
        ->setUserExternalId('USER-001')
        ->setDate('2024-01-01')
        ->setAmount(1000.00)
        ->setProbability(75);

    // Not setting close_date
    $data = $sale->toArray();

    expect($data)->not->toHaveKey('close_date');
});

it('can create sale from array with new fields', function () {
    $data = [
        'identifiers' => [
            'external_id' => 'SALE-001',
        ],
        'customer' => [
            'customer_number' => 'CUST-123',
        ],
        'subject' => 'Test Sale',
        'sale_status' => 'won',
        'selling_status' => 'sale',
        'user_external_id' => 'USER-001',
        'date' => '2024-01-01',
        'amount' => 1000.00,
        'probability' => 100,
        'create_actions' => [
            [
                'type' => 'remind',
                'remind_date' => '2024-01-15T10:00:00',
                'subject' => 'Send thank you note',
            ],
        ],
    ];

    $sale = Sale::fromArray($data);

    expect($sale->getSaleStatus())->toBe('won');
    expect($sale->getCreateActions())->toBe($data['create_actions']);
});

<?php

use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;

test('organization can be created with basic data', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->setCustomerNumber('CUST12345');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('meta')
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('identifiers')
        ->and($data['name'])->toBe('Acme Inc.')
        ->and($data['identifiers']['customer_number'])->toBe('CUST12345');
});

test('organization can include address data', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->createAddress('12345', 'New York', 'US');
    $organization->setRoute('Broadway');
    $organization->setHouseNumber('42');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('address')
        ->and($data['address'])->toHaveKey('postal_code')
        ->and($data['address'])->toHaveKey('locality')
        ->and($data['address'])->toHaveKey('country')
        ->and($data['address'])->toHaveKey('route')
        ->and($data['address'])->toHaveKey('house_number')
        ->and($data['address']['postal_code'])->toBe('12345')
        ->and($data['address']['locality'])->toBe('New York')
        ->and($data['address']['country'])->toBe('US')
        ->and($data['address']['route'])->toBe('Broadway')
        ->and($data['address']['house_number'])->toBe('42');
});

test('organization can include communications data', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->setEmail('info@acme.com');
    $organization->setPhone('+1 555-123-4567');
    $organization->setMobilePhone('+1 555-987-6543');
    $organization->setFax('+1 555-123-9876');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('communications')
        ->and($data['communications'])->toHaveKey('email')
        ->and($data['communications'])->toHaveKey('phone_number')
        ->and($data['communications'])->toHaveKey('mobile_phone')
        ->and($data['communications'])->toHaveKey('fax')
        ->and($data['communications']['email'])->toBe('info@acme.com')
        ->and($data['communications']['phone_number'])->toBe('+1 555-123-4567')
        ->and($data['communications']['mobile_phone'])->toBe('+1 555-987-6543')
        ->and($data['communications']['fax'])->toBe('+1 555-123-9876');
});

test('organization can include website data', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->setWebsite('https://acme.com');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('social_media')
        ->and($data['social_media'])->toHaveKey('web')
        ->and($data['social_media']['web'])->toBe('https://acme.com');
});

test('organization can include business information', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->setVatId('DE123456789');
    $organization->setIban('DE89 3704 0044 0532 0130 00');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('business_information')
        ->and($data['business_information'])->toHaveKey('vat_id')
        ->and($data['business_information'])->toHaveKey('iban')
        ->and($data['business_information']['vat_id'])->toBe('DE123456789')
        ->and($data['business_information']['iban'])->toBe('DE89 3704 0044 0532 0130 00');
});

test('organization can include agent information', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->setAgentExternalId('AGENT123');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('agent')
        ->and($data['agent'])->toHaveKey('external_id')
        ->and($data['agent']['external_id'])->toBe('AGENT123');
});

test('organization can include contacts', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->addContact('John', 'Doe', [
        'email' => 'john.doe@acme.com',
        'phone' => '+1 555-123-4567',
        'job_title' => 'CEO'
    ]);
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('contacts')
        ->and($data['contacts'])->toBeArray()
        ->and($data['contacts'])->toHaveCount(1)
        ->and($data['contacts'][0])->toHaveKey('firstname')
        ->and($data['contacts'][0])->toHaveKey('lastname')
        ->and($data['contacts'][0]['firstname'])->toBe('John')
        ->and($data['contacts'][0]['lastname'])->toBe('Doe')
        ->and($data['contacts'][0]['communications']['email'])->toBe('john.doe@acme.com')
        ->and($data['contacts'][0]['communications']['phone_number'])->toBe('+1 555-123-4567')
        ->and($data['contacts'][0]['employment']['job_title'])->toBe('CEO');
});

test('organization can include custom data', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.');
    $organization->setCustomField('industry', 'Technology');
    $organization->setCustomField('founded', 1985);
    $organization->setCustomField('is_active', true);
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('custom_data')
        ->and($data['custom_data'])->toHaveKey('industry')
        ->and($data['custom_data'])->toHaveKey('founded')
        ->and($data['custom_data'])->toHaveKey('is_active')
        ->and($data['custom_data']['industry'])->toBe('Technology')
        ->and($data['custom_data']['founded'])->toBe(1985)
        ->and($data['custom_data']['is_active'])->toBe(true);
});

test('organization can build complete data structure', function () {
    $organization = new Organization();
    $organization->setName('Acme Inc.')
        ->setAdditionalName('Acme Corporation')
        ->setCustomerNumber('CUST12345')
        ->createAddress('12345', 'New York', 'US')
            ->setRoute('Broadway')
            ->setHouseNumber('42')
        ->setEmail('info@acme.com')
        ->setPhone('+1 555-123-4567')
        ->setWebsite('https://acme.com')
        ->setVatId('DE123456789')
        ->setIban('DE89 3704 0044 0532 0130 00')
        ->setAgentExternalId('AGENT123')
        ->addContact('John', 'Doe', [
            'email' => 'john.doe@acme.com',
            'phone' => '+1 555-123-4567',
            'job_title' => 'CEO'
        ])
        ->setCustomField('industry', 'Technology');
    
    $data = $organization->toArray();
    
    expect($data)->toBeArray()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('additional_name')
        ->and($data)->toHaveKey('identifiers')
        ->and($data)->toHaveKey('address')
        ->and($data)->toHaveKey('communications')
        ->and($data)->toHaveKey('social_media')
        ->and($data)->toHaveKey('business_information')
        ->and($data)->toHaveKey('agent')
        ->and($data)->toHaveKey('contacts')
        ->and($data)->toHaveKey('custom_data')
        ->and($data['name'])->toBe('Acme Inc.')
        ->and($data['additional_name'])->toBe('Acme Corporation')
        ->and($data['identifiers']['customer_number'])->toBe('CUST12345')
        ->and($data['address']['postal_code'])->toBe('12345')
        ->and($data['contacts'])->toHaveCount(1)
        ->and($data['contacts'][0]['firstname'])->toBe('John');
});
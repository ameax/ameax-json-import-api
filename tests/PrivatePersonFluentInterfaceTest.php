<?php

use Ameax\AmeaxJsonImportApi\Models\Address;
use Ameax\AmeaxJsonImportApi\Models\Agent;
use Ameax\AmeaxJsonImportApi\Models\Communications;
use Ameax\AmeaxJsonImportApi\Models\Identifiers;
use Ameax\AmeaxJsonImportApi\Models\PrivatePerson;

test('privateperson provides fluent interface', function () {
    $person = new PrivatePerson;

    $result = $person->setFirstName('John')
        ->setLastName('Doe')
        ->setSalutation('Mr.')
        ->setHonorifics('Dr.')
        ->setDateOfBirth('1990-01-15')
        ->setCustomerNumber('CUST12345')
        ->createAddress('12345', 'New York', 'US')
        ->setRoute('Broadway')
        ->setHouseNumber('42')
        ->setEmail('john.doe@example.com')
        ->setPhone('+1 555-123-4567')
        ->setMobilePhone('+1 555-987-6543')
        ->setFax('+1 555-123-9876')
        ->setAgentExternalId('AGENT123')
        ->setCustomField('occupation', 'Engineer');

    expect($result)->toBeInstanceOf(PrivatePerson::class)
        ->and($person->getFirstName())->toBe('John')
        ->and($person->getLastName())->toBe('Doe')
        ->and($person->getSalutation())->toBe('Mr.')
        ->and($person->getHonorifics())->toBe('Dr.')
        ->and($person->getDateOfBirth())->toBe('1990-01-15')
        ->and($person->getCustomerNumber())->toBe('CUST12345')
        ->and($person->getAddress())->toBeInstanceOf(Address::class)
        ->and($person->getAddress()->getPostalCode())->toBe('12345')
        ->and($person->getEmail())->toBe('john.doe@example.com')
        ->and($person->getPhone())->toBe('+1 555-123-4567')
        ->and($person->getMobilePhone())->toBe('+1 555-987-6543')
        ->and($person->getFax())->toBe('+1 555-123-9876')
        ->and($person->getAgent())->toBeInstanceOf(Agent::class)
        ->and($person->getAgent()->getExternalId())->toBe('AGENT123')
        ->and($person->getCustomData())->toBeArray()
        ->and($person->getCustomData())->toHaveKey('occupation')
        ->and($person->getCustomData()['occupation'])->toBe('Engineer');
});

test('privateperson objects can be created and set', function () {
    $person = new PrivatePerson;

    // Create and set address
    $address = new Address;
    $address->setPostalCode('12345')
        ->setLocality('New York')
        ->setCountry('US')
        ->setRoute('Broadway')
        ->setHouseNumber('42');
    $person->setAddress($address);

    // Create and set communications
    $communications = new Communications;
    $communications->setEmail('john.doe@example.com')
        ->setPhoneNumber('+1 555-123-4567')
        ->setMobilePhone('+1 555-987-6543')
        ->setFax('+1 555-123-9876');
    $person->setCommunications($communications);

    // Create and set agent
    $agent = new Agent;
    $agent->setExternalId('AGENT123');
    $person->setAgent($agent);

    // Create and set identifiers
    $identifiers = new Identifiers;
    $identifiers->setCustomerNumber('CUST12345');
    $person->setIdentifiers($identifiers);

    $data = $person->toArray();

    expect($data)->toHaveKey('address')
        ->and($data)->toHaveKey('communications')
        ->and($data)->toHaveKey('agent')
        ->and($data)->toHaveKey('identifiers')
        ->and($data['address']['postal_code'])->toBe('12345')
        ->and($data['communications']['email'])->toBe('john.doe@example.com')
        ->and($data['agent']['external_id'])->toBe('AGENT123')
        ->and($data['identifiers']['customer_number'])->toBe('CUST12345');
});

test('privateperson supports type conversion for custom fields', function () {
    $person = new PrivatePerson;

    // Test boolean conversion
    $person->setCustomField('active_true_string', 'true');
    $person->setCustomField('active_false_string', 'false');
    $person->setCustomField('active_true_uppercase', 'TRUE');
    $person->setCustomField('active_false_uppercase', 'FALSE');
    $person->setCustomField('active_one', '1');
    $person->setCustomField('active_zero', '0');

    // Test integer conversion
    $person->setCustomField('age_string', '30');

    $data = $person->toArray();

    expect($data['custom_data']['active_true_string'])->toBeTrue()
        ->and($data['custom_data']['active_false_string'])->toBeFalse()
        ->and($data['custom_data']['active_true_uppercase'])->toBeTrue()
        ->and($data['custom_data']['active_false_uppercase'])->toBeFalse()
        ->and($data['custom_data']['active_one'])->toBeTrue()
        ->and($data['custom_data']['active_zero'])->toBeFalse()
        ->and($data['custom_data']['age_string'])->toBe(30)
        ->and($data['custom_data']['age_string'])->toBeInt();
});

test('privateperson supports bulk setting of custom fields', function () {
    $person = new PrivatePerson;

    $customData = [
        'occupation' => 'Engineer',
        'age' => 30,
        'is_active' => true,
        'salary' => '100000',
    ];

    $person->setCustomData($customData);

    $data = $person->toArray();

    expect($data)->toHaveKey('custom_data')
        ->and($data['custom_data'])->toHaveCount(4)
        ->and($data['custom_data'])->toBe($customData);
});

test('privateperson allows removing custom fields', function () {
    $person = new PrivatePerson;

    $person->setCustomField('occupation', 'Engineer');
    $person->setCustomField('age', 30);
    $person->setCustomField('is_active', true);

    // Remove a field by setting it to null
    $person->setCustomField('age', null);

    $data = $person->toArray();

    expect($data['custom_data'])->toHaveKey('occupation')
        ->and($data['custom_data'])->toHaveKey('is_active')
        ->and($data['custom_data'])->not->toHaveKey('age');
});

test('privateperson handles customer number conversion', function () {
    $person = new PrivatePerson;

    // Test with integer
    $person->setCustomerNumber(12345);
    expect($person->getCustomerNumber())->toBe('12345')
        ->and($person->getCustomerNumber())->toBeString();

    // Test with string
    $person->setCustomerNumber('CUST-9876');
    expect($person->getCustomerNumber())->toBe('CUST-9876');
});

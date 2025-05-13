<?php

use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\Models\Address;
use Ameax\AmeaxJsonImportApi\Models\Communications;
use Ameax\AmeaxJsonImportApi\Models\Contact;
use Ameax\AmeaxJsonImportApi\Models\Identifiers;
use Ameax\AmeaxJsonImportApi\Models\BusinessInformation;
use Ameax\AmeaxJsonImportApi\Models\SocialMedia;
use Ameax\AmeaxJsonImportApi\Models\Agent;

test('organization provides fluent interface', function () {
    $organization = new Organization();
    
    $result = $organization->setName('Acme Inc.')
        ->setAdditionalName('Acme Corporation')
        ->setCustomerNumber('CUST12345')
        ->createAddress('12345', 'New York', 'US')
        ->setRoute('Broadway')
        ->setHouseNumber('42')
        ->setEmail('info@acme.com')
        ->setPhone('+1 555-123-4567')
        ->setMobilePhone('+1 555-987-6543')
        ->setFax('+1 555-123-9876')
        ->setWebsite('https://acme.com')
        ->setVatId('DE123456789')
        ->setIban('DE89 3704 0044 0532 0130 00')
        ->setAgentExternalId('AGENT123')
        ->setCustomField('industry', 'Technology');
    
    expect($result)->toBeInstanceOf(Organization::class)
        ->and($organization->getName())->toBe('Acme Inc.')
        ->and($organization->getAdditionalName())->toBe('Acme Corporation')
        ->and($organization->getCustomerNumber())->toBe('CUST12345')
        ->and($organization->getAddress())->toBeInstanceOf(Address::class)
        ->and($organization->getAddress()->getPostalCode())->toBe('12345')
        ->and($organization->getEmail())->toBe('info@acme.com')
        ->and($organization->getPhone())->toBe('+1 555-123-4567')
        ->and($organization->getMobilePhone())->toBe('+1 555-987-6543')
        ->and($organization->getFax())->toBe('+1 555-123-9876')
        ->and($organization->getWebsite())->toBe('https://acme.com')
        ->and($organization->getVatId())->toBe('DE123456789')
        ->and($organization->getIban())->toBe('DE89 3704 0044 0532 0130 00')
        ->and($organization->getAgent())->toBeInstanceOf(Agent::class)
        ->and($organization->getAgent()->getExternalId())->toBe('AGENT123')
        ->and($organization->getCustomData())->toBeArray()
        ->and($organization->getCustomData())->toHaveKey('industry')
        ->and($organization->getCustomData()['industry'])->toBe('Technology');
});

test('organization objects can be created and set', function () {
    $organization = new Organization();
    
    // Create and set address
    $address = new Address();
    $address->setPostalCode('12345')
        ->setLocality('New York')
        ->setCountry('US')
        ->setRoute('Broadway')
        ->setHouseNumber('42');
    $organization->setAddress($address);
    
    // Create and set communications
    $communications = new Communications();
    $communications->setEmail('info@acme.com')
        ->setPhoneNumber('+1 555-123-4567')
        ->setMobilePhone('+1 555-987-6543')
        ->setFax('+1 555-123-9876');
    $organization->setCommunications($communications);
    
    // Create and set social media
    $socialMedia = new SocialMedia();
    $socialMedia->setWeb('https://acme.com');
    $organization->setSocialMedia($socialMedia);
    
    // Create and set business information
    $businessInfo = new BusinessInformation();
    $businessInfo->setVatId('DE123456789')
        ->setIban('DE89 3704 0044 0532 0130 00');
    $organization->setBusinessInformation($businessInfo);
    
    // Create and set agent
    $agent = new Agent();
    $agent->setExternalId('AGENT123');
    $organization->setAgent($agent);
    
    // Create and add contact
    $contact = new Contact();
    $contact->setFirstName('John')
        ->setLastName('Doe')
        ->setEmail('john.doe@acme.com')
        ->setPhone('+1 555-123-4567')
        ->setJobTitle('CEO');
    $organization->addContactObject($contact);
    
    $data = $organization->toArray();
    
    expect($data)->toHaveKey('address')
        ->and($data)->toHaveKey('communications')
        ->and($data)->toHaveKey('social_media')
        ->and($data)->toHaveKey('business_information')
        ->and($data)->toHaveKey('agent')
        ->and($data)->toHaveKey('contacts')
        ->and($data['address']['postal_code'])->toBe('12345')
        ->and($data['communications']['email'])->toBe('info@acme.com')
        ->and($data['social_media']['web'])->toBe('https://acme.com')
        ->and($data['business_information']['vat_id'])->toBe('DE123456789')
        ->and($data['agent']['external_id'])->toBe('AGENT123')
        ->and($data['contacts'][0]['firstname'])->toBe('John');
});

test('organization supports type conversion for custom fields', function () {
    $organization = new Organization();
    
    // Test boolean conversion
    $organization->setCustomField('active_true_string', 'true');
    $organization->setCustomField('active_false_string', 'false');
    $organization->setCustomField('active_true_uppercase', 'TRUE');
    $organization->setCustomField('active_false_uppercase', 'FALSE');
    $organization->setCustomField('active_one', '1');
    $organization->setCustomField('active_zero', '0');
    
    // Test integer conversion
    $organization->setCustomField('count_string', '42');
    
    $data = $organization->toArray();
    
    expect($data['custom_data']['active_true_string'])->toBeTrue()
        ->and($data['custom_data']['active_false_string'])->toBeFalse()
        ->and($data['custom_data']['active_true_uppercase'])->toBeTrue()
        ->and($data['custom_data']['active_false_uppercase'])->toBeFalse()
        ->and($data['custom_data']['active_one'])->toBeTrue()
        ->and($data['custom_data']['active_zero'])->toBeFalse()
        ->and($data['custom_data']['count_string'])->toBe(42)
        ->and($data['custom_data']['count_string'])->toBeInt();
});

test('organization supports bulk setting of custom fields', function () {
    $organization = new Organization();
    
    $customData = [
        'industry' => 'Technology',
        'founded' => 1985,
        'is_active' => true,
        'revenue' => '10000000'
    ];
    
    $organization->setCustomData($customData);
    
    $data = $organization->toArray();
    
    expect($data)->toHaveKey('custom_data')
        ->and($data['custom_data'])->toHaveCount(4)
        ->and($data['custom_data'])->toBe($customData);
});

test('organization allows removing custom fields', function () {
    $organization = new Organization();
    
    $organization->setCustomField('industry', 'Technology');
    $organization->setCustomField('founded', 1985);
    $organization->setCustomField('is_active', true);
    
    // Remove a field by setting it to null
    $organization->setCustomField('founded', null);
    
    $data = $organization->toArray();
    
    expect($data['custom_data'])->toHaveKey('industry')
        ->and($data['custom_data'])->toHaveKey('is_active')
        ->and($data['custom_data'])->not->toHaveKey('founded');
});
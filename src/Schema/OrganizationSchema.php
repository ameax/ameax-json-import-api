<?php

namespace Ameax\AmeaxJsonImportApi\Schema;

class OrganizationSchema
{
    public const DOCUMENT_TYPE = 'ameax_organization_account';

    public const SCHEMA_VERSION = '1.0';

    /**
     * Create a new organization schema with required fields
     *
     * @param  string  $name  Organization name
     * @param  string  $postalCode  Postal code
     * @param  string  $locality  City/town
     * @param  string  $country  Country code (ISO 3166-1 alpha-2)
     */
    public static function create(string $name, string $postalCode, string $locality, string $country): array
    {
        return [
            'document_type' => self::DOCUMENT_TYPE,
            'schema_version' => self::SCHEMA_VERSION,
            'name' => $name,
            'address' => [
                'postal_code' => $postalCode,
                'locality' => $locality,
                'country' => $country,
            ],
        ];
    }

    /**
     * Add contact to organization
     *
     * @param  array  $organization  Organization data
     * @param  string  $firstName  First name
     * @param  string  $lastName  Last name
     * @param  array  $additionalData  Additional contact data
     */
    public static function addContact(array $organization, string $firstName, string $lastName, array $additionalData = []): array
    {
        if (! isset($organization['contacts'])) {
            $organization['contacts'] = [];
        }

        $contact = array_merge([
            'first_name' => $firstName,
            'last_name' => $lastName,
        ], $additionalData);

        $organization['contacts'][] = $contact;

        return $organization;
    }
}

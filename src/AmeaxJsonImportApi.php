<?php

namespace Ameax\AmeaxJsonImportApi;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\Models\PrivatePerson;
use Ameax\AmeaxJsonImportApi\Models\Address;
use Ameax\AmeaxJsonImportApi\Models\Contact;
use Ameax\AmeaxJsonImportApi\Models\Meta;
use Ameax\AmeaxJsonImportApi\Models\Identifiers;
use Ameax\AmeaxJsonImportApi\Models\SocialMedia;
use Ameax\AmeaxJsonImportApi\Models\Communications;
use Ameax\AmeaxJsonImportApi\Models\BusinessInformation;
use Ameax\AmeaxJsonImportApi\Models\Agent;
use Ameax\AmeaxJsonImportApi\Models\Employment;

class AmeaxJsonImportApi
{
    protected HttpClient $client;
    protected string $apiKey;
    protected string $baseUrl;

    /**
     * Create a new API client instance
     *
     * @param string $apiKey Your Ameax API key
     * @param string $host The API host URL (e.g., https://your-database.ameax.de)
     */
    public function __construct(string $apiKey, string $host)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($host, '/') . '/rest-api';

        $this->client = new HttpClient([
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a new empty organization
     *
     * @return Organization A new organization instance
     */
    public function createOrganization(): Organization
    {
        $organization = new Organization();
        return $organization->setApiClient($this);
    }

    /**
     * Create an organization from an existing array of data
     *
     * @param array $data The organization data
     * @return Organization
     */
    public function organizationFromArray(array $data): Organization
    {
        $organization = Organization::fromArray($data);
        $organization->setApiClient($this);

        return $organization;
    }

    /**
     * Create a new empty address
     *
     * @return Address A new address instance
     */
    public function createAddress(): Address
    {
        return new Address();
    }

    /**
     * Create a new empty contact
     *
     * @return Contact A new contact instance
     */
    public function createContact(): Contact
    {
        return new Contact();
    }

    /**
     * Create a new empty private person
     *
     * @return PrivatePerson A new private person instance
     */
    public function createPrivatePerson(): PrivatePerson
    {
        $privatePerson = new PrivatePerson();
        return $privatePerson->setApiClient($this);
    }

    /**
     * Create a private person from an existing array of data
     *
     * @param array $data The private person data
     * @return PrivatePerson
     */
    public function privatePersonFromArray(array $data): PrivatePerson
    {
        $privatePerson = PrivatePerson::fromArray($data);
        $privatePerson->setApiClient($this);

        return $privatePerson;
    }

    /**
     * Send organization data to Ameax API
     *
     * @param array $organization The organization data
     * @return array The API response
     * @throws \Exception If request fails
     * @internal This is used by the Organization class and generally should not be called directly
     */
    public function sendOrganization(array $organization): array
    {
        // Ensure meta.document_type and meta.schema_version are set correctly
        if (!isset($organization['meta'])) {
            $organization['meta'] = [
                'document_type' => Meta::DOCUMENT_TYPE_ORGANIZATION,
                'schema_version' => Meta::SCHEMA_VERSION,
            ];
        } else {
            $organization['meta']['document_type'] = Meta::DOCUMENT_TYPE_ORGANIZATION;
            if (!isset($organization['meta']['schema_version'])) {
                $organization['meta']['schema_version'] = Meta::SCHEMA_VERSION;
            }
        }

        try {
            $response = $this->client->post("{$this->baseUrl}/imports", [
                'json' => $organization,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception("Error sending organization data to Ameax: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Send private person data to Ameax API
     *
     * @param array $privatePerson The private person data
     * @return array The API response
     * @throws \Exception If request fails
     * @internal This is used by the PrivatePerson class and generally should not be called directly
     */
    public function sendPrivatePerson(array $privatePerson): array
    {
        // Ensure meta.document_type and meta.schema_version are set correctly
        if (!isset($privatePerson['meta'])) {
            $privatePerson['meta'] = [
                'document_type' => Meta::DOCUMENT_TYPE_PRIVATE_PERSON,
                'schema_version' => Meta::SCHEMA_VERSION,
            ];
        } else {
            $privatePerson['meta']['document_type'] = Meta::DOCUMENT_TYPE_PRIVATE_PERSON;
            if (!isset($privatePerson['meta']['schema_version'])) {
                $privatePerson['meta']['schema_version'] = Meta::SCHEMA_VERSION;
            }
        }

        try {
            $response = $this->client->post("{$this->baseUrl}/imports", [
                'json' => $privatePerson,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception("Error sending private person data to Ameax: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

}

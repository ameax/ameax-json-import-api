<?php

namespace Ameax\AmeaxJsonImportApi;

use Ameax\AmeaxJsonImportApi\Models\Address;
use Ameax\AmeaxJsonImportApi\Models\Contact;
use Ameax\AmeaxJsonImportApi\Models\Meta;
use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\Models\PrivatePerson;
use Ameax\AmeaxJsonImportApi\Models\Receipt;
use Ameax\AmeaxJsonImportApi\Models\Sale;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class AmeaxJsonImportApi
{
    protected HttpClient $client;

    protected string $apiKey;

    protected string $baseUrl;

    /**
     * Create a new API client instance
     *
     * @param  string  $apiKey  Your Ameax API key
     * @param  string  $host  The API host URL (e.g., https://your-database.ameax.de)
     */
    public function __construct(string $apiKey, string $host)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($host, '/').'/rest-api';

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
        $organization = new Organization;

        return $organization->setApiClient($this);
    }

    /**
     * Create an organization from an existing array of data
     *
     * @param  array<string, mixed>  $data  The organization data
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
        return new Address;
    }

    /**
     * Create a new empty contact
     *
     * @return Contact A new contact instance
     */
    public function createContact(): Contact
    {
        return new Contact;
    }

    /**
     * Create a new empty private person
     *
     * @return PrivatePerson A new private person instance
     */
    public function createPrivatePerson(): PrivatePerson
    {
        $privatePerson = new PrivatePerson;

        return $privatePerson->setApiClient($this);
    }

    /**
     * Create a private person from an existing array of data
     *
     * @param  array<string, mixed>  $data  The private person data
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
     * @param  array<string, mixed>  $organization  The organization data
     * @return array<string, mixed> The API response
     *
     * @throws \Exception If request fails
     *
     * @internal This is used by the Organization class and generally should not be called directly
     */
    public function sendOrganization(array $organization): array
    {
        // Ensure meta.document_type and meta.schema_version are set correctly
        if (! isset($organization['meta'])) {
            $organization['meta'] = [
                'document_type' => Meta::DOCUMENT_TYPE_ORGANIZATION,
                'schema_version' => Meta::SCHEMA_VERSION,
            ];
        } else {
            $organization['meta']['document_type'] = Meta::DOCUMENT_TYPE_ORGANIZATION;
            if (! isset($organization['meta']['schema_version'])) {
                $organization['meta']['schema_version'] = Meta::SCHEMA_VERSION;
            }
        }

        try {
            $response = $this->client->post("{$this->baseUrl}/imports", [
                'json' => $organization,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception('Error sending organization data to Ameax: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Send private person data to Ameax API
     *
     * @param  array<string, mixed>  $privatePerson  The private person data
     * @return array<string, mixed> The API response
     *
     * @throws \Exception If request fails
     *
     * @internal This is used by the PrivatePerson class and generally should not be called directly
     */
    public function sendPrivatePerson(array $privatePerson): array
    {
        // Ensure meta.document_type and meta.schema_version are set correctly
        if (! isset($privatePerson['meta'])) {
            $privatePerson['meta'] = [
                'document_type' => Meta::DOCUMENT_TYPE_PRIVATE_PERSON,
                'schema_version' => Meta::SCHEMA_VERSION,
            ];
        } else {
            $privatePerson['meta']['document_type'] = Meta::DOCUMENT_TYPE_PRIVATE_PERSON;
            if (! isset($privatePerson['meta']['schema_version'])) {
                $privatePerson['meta']['schema_version'] = Meta::SCHEMA_VERSION;
            }
        }

        try {
            $response = $this->client->post("{$this->baseUrl}/imports", [
                'json' => $privatePerson,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception('Error sending private person data to Ameax: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create a new empty receipt
     *
     * @return Receipt A new receipt instance
     */
    public function createReceipt(): Receipt
    {
        $receipt = new Receipt;

        return $receipt->setApiClient($this);
    }

    /**
     * Create a receipt from an existing array of data
     *
     * @param  array<string, mixed>  $data  The receipt data
     */
    public function receiptFromArray(array $data): Receipt
    {
        $receipt = Receipt::fromArray($data);
        $receipt->setApiClient($this);

        return $receipt;
    }

    /**
     * Send receipt data to Ameax API
     *
     * @param  array<string, mixed>  $receipt  The receipt data
     * @return array<string, mixed> The API response
     *
     * @throws \Exception If request fails
     *
     * @internal This is used by the Receipt class and generally should not be called directly
     */
    public function sendReceipt(array $receipt): array
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/imports", [
                'json' => $receipt,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception('Error sending receipt data to Ameax: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create a new empty sale
     *
     * @return Sale A new sale instance
     */
    public function createSale(): Sale
    {
        $sale = new Sale;

        return $sale->setApiClient($this);
    }

    /**
     * Create a sale from an existing array of data
     *
     * @param  array<string, mixed>  $data  The sale data
     */
    public function saleFromArray(array $data): Sale
    {
        $sale = Sale::fromArray($data);
        $sale->setApiClient($this);

        return $sale;
    }

    /**
     * Send sale data to Ameax API
     *
     * @param  array<string, mixed>  $sale  The sale data
     * @return array<string, mixed> The API response
     *
     * @throws \Exception If request fails
     *
     * @internal This is used by the Sale class and generally should not be called directly
     */
    public function sendSale(array $sale): array
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/imports", [
                'json' => $sale,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception('Error sending sale data to Ameax: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}

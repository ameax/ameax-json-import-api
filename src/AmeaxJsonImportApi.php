<?php

namespace Ameax\AmeaxJsonImportApi;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
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
use Ameax\AmeaxJsonImportApi\Validation\SchemaValidator;

class AmeaxJsonImportApi
{
    protected HttpClient $client;
    protected string $apiKey;
    protected string $baseUrl;
    protected ?string $schemasPath;
    
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
        $this->schemasPath = null;
        
        $this->client = new HttpClient([
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
    
    /**
     * Set the path to custom JSON schema files
     *
     * @param string $schemasPath Path to the directory containing schema files
     * @return $this
     */
    public function setSchemasPath(string $schemasPath): self
    {
        $this->schemasPath = rtrim($schemasPath, '/');
        return $this;
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
     * @param bool $validate Whether to validate the data immediately
     * @return Organization
     * @throws ValidationException If validation fails and $validate is true
     */
    public function organizationFromArray(array $data, bool $validate = true): Organization
    {
        $organization = Organization::fromArray($data, !$validate);
        $organization->setApiClient($this);
        
        if ($validate) {
            $organization->validate();
        }
        
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
     * @param bool $validate Whether to validate the data immediately
     * @return PrivatePerson
     * @throws ValidationException If validation fails and $validate is true
     */
    public function privatePersonFromArray(array $data, bool $validate = true): PrivatePerson
    {
        $privatePerson = PrivatePerson::fromArray($data, !$validate);
        $privatePerson->setApiClient($this);
        
        if ($validate) {
            $privatePerson->validate();
        }
        
        return $privatePerson;
    }
    
    /**
     * Send organization data to Ameax API
     *
     * @param array $organization The organization data
     * @return array The API response
     * @throws \Exception If validation or request fails
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
        
        // For schemas path validation
        $schemaFile = $this->getSchemaFilePath(Meta::DOCUMENT_TYPE_ORGANIZATION);
        if (file_exists($schemaFile)) {
            $this->validateAgainstSchema($organization, Meta::DOCUMENT_TYPE_ORGANIZATION);
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
     * @throws \Exception If validation or request fails
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
        
        // For schemas path validation
        $schemaFile = $this->getSchemaFilePath(Meta::DOCUMENT_TYPE_PRIVATE_PERSON);
        if (file_exists($schemaFile)) {
            $this->validateAgainstSchema($privatePerson, Meta::DOCUMENT_TYPE_PRIVATE_PERSON);
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
    
    /**
     * Validate data against the appropriate JSON schema file
     *
     * @param array $data The data to validate
     * @param string $documentType The type of document being validated
     * @return bool True if validation passes
     * @throws ValidationException If validation fails
     */
    protected function validateAgainstSchema(array $data, string $documentType): bool
    {
        $schemaFile = $this->getSchemaFilePath($documentType);
        
        if (!file_exists($schemaFile)) {
            throw new \InvalidArgumentException("Schema file not found for document type: {$documentType}");
        }
        
        return SchemaValidator::validate($data, $schemaFile);
    }
    
    /**
     * Get the schema file path for a document type
     *
     * @param string $documentType The document type
     * @return string The schema file path
     */
    protected function getSchemaFilePath(string $documentType): string
    {
        if ($this->schemasPath) {
            return rtrim($this->schemasPath, '/') . "/{$documentType}.json";
        }
        
        return __DIR__ . "/../resources/schemas/{$documentType}.json";
    }
}
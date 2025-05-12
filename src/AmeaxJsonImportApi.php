<?php

namespace Ameax\AmeaxJsonImportApi;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use JsonSchema\Validator;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Models\Organization;
use Ameax\AmeaxJsonImportApi\Models\Address;
use Ameax\AmeaxJsonImportApi\Models\Contact;

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
     * @param string|null $schemasPath Optional path to custom schema files
     */
    public function __construct(string $apiKey, string $host, ?string $schemasPath = null)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($host, '/') . '/rest-api';
        $this->schemasPath = $schemasPath;
        
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
     * @throws ValidationException If validation fails
     */
    public function organizationFromArray(array $data): Organization
    {
        $organization = Organization::fromArray($data);
        return $organization->setApiClient($this);
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
     * Send organization data to Ameax API
     *
     * @param array $organization The organization data
     * @return array The API response
     * @throws \Exception If validation or request fails
     * @internal This is used by the Organization class and generally should not be called directly
     */
    public function sendOrganization(array $organization): array
    {
        if (!isset($organization['document_type']) || $organization['document_type'] !== Organization::DOCUMENT_TYPE) {
            throw new \InvalidArgumentException('Invalid organization data: document_type must be ' . Organization::DOCUMENT_TYPE);
        }
        
        // For schemas path validation
        $schemaFile = $this->getSchemaFilePath(Organization::DOCUMENT_TYPE);
        if (file_exists($schemaFile)) {
            $this->validateAgainstSchema($organization, Organization::DOCUMENT_TYPE);
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
        
        $schema = json_decode(file_get_contents($schemaFile));
        $validator = new Validator();
        $validator->validate(json_decode(json_encode($data)), $schema);
        
        if (!$validator->isValid()) {
            $errors = [];
            foreach ($validator->getErrors() as $error) {
                $errors[] = sprintf("[%s] %s", $error['property'], $error['message']);
            }
            throw new ValidationException($errors);
        }
        
        return true;
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
<?php

namespace Ameax\AmeaxJsonImportApi;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use JsonSchema\Validator;
use Ameax\AmeaxJsonImportApi\Schema\OrganizationSchema;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

class AmeaxJsonImportApi
{
    protected HttpClient $client;
    protected string $apiKey;
    protected string $baseUrl;
    
    public function __construct(string $apiKey, string $databaseName)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = "https://{$databaseName}.ameax.de/rest-api";
        
        $this->client = new HttpClient([
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
    
    /**
     * Send organization data to Ameax API
     * 
     * @param array $organization The organization data
     * @return array The API response
     * @throws \Exception If validation or request fails
     */
    public function sendOrganization(array $organization): array
    {
        if (!isset($organization['document_type']) || $organization['document_type'] !== OrganizationSchema::DOCUMENT_TYPE) {
            throw new \InvalidArgumentException('Invalid organization data: document_type must be ' . OrganizationSchema::DOCUMENT_TYPE);
        }
        
        // Validate the organization data against the JSON schema
        $this->validate($organization, OrganizationSchema::DOCUMENT_TYPE);
        
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
     * Validate data against the appropriate JSON schema
     * 
     * @param array $data The data to validate
     * @param string $documentType The type of document being validated
     * @return bool True if validation passes
     * @throws ValidationException If validation fails
     */
    protected function validate(array $data, string $documentType): bool
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
        $customPath = config('ameax-json-import-api.schemas_path');
        
        if ($customPath) {
            return rtrim($customPath, '/') . "/{$documentType}.json";
        }
        
        return __DIR__ . "/../resources/schemas/{$documentType}.json";
    }
    
    /**
     * Create a new organization with required fields
     * 
     * @param string $name Organization name
     * @param string $postalCode Postal code
     * @param string $locality City/town
     * @param string $country Country code (ISO 3166-1 alpha-2)
     * @return array The created organization data
     */
    public function createOrganization(string $name, string $postalCode, string $locality, string $country): array
    {
        return OrganizationSchema::create($name, $postalCode, $locality, $country);
    }
    
    /**
     * Add a contact to an organization
     * 
     * @param array $organization The organization data
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param array $additionalData Additional contact data
     * @return array The updated organization data
     */
    public function addOrganizationContact(array $organization, string $firstName, string $lastName, array $additionalData = []): array
    {
        return OrganizationSchema::addContact($organization, $firstName, $lastName, $additionalData);
    }
}

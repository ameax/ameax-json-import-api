<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use InvalidArgumentException;

class Organization extends BaseModel
{
    public const DOCUMENT_TYPE = 'ameax_organization_account';
    public const SCHEMA_VERSION = '1.0';
    
    /**
     * @var AmeaxJsonImportApi|null The API client to use for submission
     */
    protected ?AmeaxJsonImportApi $apiClient = null;
    
    /**
     * Create a new organization with required fields
     *
     * @param string $name The organization name
     * @param string $postalCode The postal code
     * @param string $locality The city/town
     * @param string $country The country code (ISO 3166-1 alpha-2)
     * @return static
     */
    public static function create(
        string $name, 
        string $postalCode, 
        string $locality, 
        string $country
    ): self {
        $instance = new static();
        
        $instance->data = [
            'document_type' => self::DOCUMENT_TYPE,
            'schema_version' => self::SCHEMA_VERSION,
            'name' => $name,
            'address' => [
                'postal_code' => $postalCode,
                'locality' => $locality,
                'country' => $country,
            ],
        ];
        
        return $instance;
    }
    
    /**
     * Set the API client for this organization (required for submission)
     *
     * @param AmeaxJsonImportApi $apiClient
     * @return $this
     */
    public function setApiClient(AmeaxJsonImportApi $apiClient): self
    {
        $this->apiClient = $apiClient;
        return $this;
    }
    
    /**
     * Add contact information to the organization
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param array $additionalData Additional contact data
     * @return $this
     */
    public function addContact(string $firstName, string $lastName, array $additionalData = []): self
    {
        if (!isset($this->data['contacts'])) {
            $this->data['contacts'] = [];
        }
        
        $contact = array_merge([
            'first_name' => $firstName,
            'last_name' => $lastName,
        ], $additionalData);
        
        $this->data['contacts'][] = $contact;
        
        return $this;
    }
    
    /**
     * Set street address
     *
     * @param string $street The street name
     * @return $this
     */
    public function setStreet(string $street): self
    {
        $this->data['address']['street'] = $street;
        return $this;
    }
    
    /**
     * Set house number
     *
     * @param string $houseNumber The house number
     * @return $this
     */
    public function setHouseNumber(string $houseNumber): self
    {
        $this->data['address']['house_number'] = $houseNumber;
        return $this;
    }
    
    /**
     * Set organization email
     *
     * @param string $email The email address
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->data['email'] = $email;
        return $this;
    }
    
    /**
     * Set organization phone
     *
     * @param string $phone The phone number
     * @return $this
     */
    public function setPhone(string $phone): self
    {
        $this->data['phone'] = $phone;
        return $this;
    }
    
    /**
     * Set organization website
     *
     * @param string $website The website URL
     * @return $this
     */
    public function setWebsite(string $website): self
    {
        $this->data['website'] = $website;
        return $this;
    }
    
    /**
     * Set organization VAT ID
     *
     * @param string $vatId The VAT ID
     * @return $this
     */
    public function setVatId(string $vatId): self
    {
        $this->data['vat_id'] = $vatId;
        return $this;
    }
    
    /**
     * Set organization tax ID
     *
     * @param string $taxId The tax ID
     * @return $this
     */
    public function setTaxId(string $taxId): self
    {
        $this->data['tax_id'] = $taxId;
        return $this;
    }
    
    /**
     * Set a custom field value in the organization data
     *
     * @param string $key The field key
     * @param mixed $value The field value
     * @return $this
     */
    public function setCustomField(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Submit the organization to the Ameax API
     *
     * @return array The API response
     * @throws \InvalidArgumentException If no API client is set
     * @throws \Exception If the API request fails
     */
    public function submit(): array
    {
        if (!$this->apiClient) {
            throw new InvalidArgumentException(
                'No API client set. Use setApiClient() before calling submit().'
            );
        }
        
        return $this->apiClient->sendOrganization($this->data);
    }
}
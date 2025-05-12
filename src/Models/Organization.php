<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;
use InvalidArgumentException;

class Organization extends BaseModel
{
    public const DOCUMENT_TYPE = 'ameax_organization_account';
    public const SCHEMA_VERSION = '1.0';
    
    /**
     * @var AmeaxJsonImportApi|null The API client to use for sending
     */
    protected ?AmeaxJsonImportApi $apiClient = null;
    
    /**
     * @var Address|null The organization address
     */
    protected ?Address $address = null;
    
    /**
     * @var array Custom data fields
     */
    protected array $customData = [];
    
    /**
     * Create a new organization with required fields
     *
     * @param string $name The organization name
     * @param string $postalCode The postal code
     * @param string $locality The city/town
     * @param string $country The country code (ISO 3166-1 alpha-2)
     * @return static
     * @throws ValidationException If validation fails
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
        ];
        
        $instance->setName($name);
        
        // Create and set address
        $address = Address::create($postalCode, $locality, $country);
        $instance->setAddress($address);
        
        return $instance;
    }
    
    /**
     * Populate the model with data using setters
     *
     * @param array $data
     * @return $this
     * @throws ValidationException If validation fails
     */
    protected function populate(array $data): self
    {
        // Set document type and schema version
        $this->data['document_type'] = self::DOCUMENT_TYPE;
        $this->data['schema_version'] = self::SCHEMA_VERSION;
        
        // Set name if provided
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }
        
        // Handle address
        if (isset($data['address']) && is_array($data['address'])) {
            $this->address = Address::fromArray($data['address']);
            $this->data['address'] = $this->address->toArray();
        }
        
        // Handle contacts
        if (isset($data['contacts']) && is_array($data['contacts'])) {
            $this->data['contacts'] = [];
            
            foreach ($data['contacts'] as $contactData) {
                $contact = Contact::fromArray($contactData);
                $this->data['contacts'][] = $contact->toArray();
            }
        }
        
        // Handle other standard fields
        $standardFields = [
            'email', 'phone', 'website', 'vat_id', 'tax_id'
        ];
        
        foreach ($standardFields as $field) {
            if (isset($data[$field])) {
                $method = 'set' . str_replace('_', '', ucwords($field, '_'));
                if (method_exists($this, $method)) {
                    $this->$method($data[$field]);
                }
            }
        }
        
        // Handle any other custom fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['document_type', 'schema_version', 'name', 'address', 'contacts', 'email', 'phone', 'website', 'vat_id', 'tax_id'])) {
                $this->setCustomField($key, $value);
            }
        }
        
        return $this;
    }
    
    /**
     * Validate the model data before saving/sending
     *
     * @return bool True if validation passes
     * @throws ValidationException If validation fails
     */
    public function validate(): bool
    {
        $errors = [];
        
        // Check required fields
        if (!isset($this->data['document_type']) || $this->data['document_type'] !== self::DOCUMENT_TYPE) {
            $errors[] = "document_type must be " . self::DOCUMENT_TYPE;
        }
        
        if (!isset($this->data['schema_version']) || $this->data['schema_version'] !== self::SCHEMA_VERSION) {
            $errors[] = "schema_version must be " . self::SCHEMA_VERSION;
        }
        
        if (!isset($this->data['name'])) {
            $errors[] = "name is required";
        }
        
        if (!isset($this->data['address'])) {
            $errors[] = "address is required";
        } elseif (is_array($this->data['address'])) {
            // Validate address if it's an array
            $requiredAddressFields = ['postal_code', 'locality', 'country'];
            foreach ($requiredAddressFields as $field) {
                if (!isset($this->data['address'][$field])) {
                    $errors[] = "address.{$field} is required";
                }
            }
        }
        
        // Validate optional fields if present
        if (isset($this->data['email'])) {
            try {
                Validator::email($this->data['email'], 'email');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if (isset($this->data['phone'])) {
            try {
                Validator::phoneNumber($this->data['phone'], 'phone');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if (isset($this->data['website'])) {
            try {
                Validator::url($this->data['website'], 'website');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        // Validate contacts if present
        if (isset($this->data['contacts']) && is_array($this->data['contacts'])) {
            foreach ($this->data['contacts'] as $index => $contact) {
                if (!isset($contact['first_name'])) {
                    $errors[] = "contacts[{$index}].first_name is required";
                }
                
                if (!isset($contact['last_name'])) {
                    $errors[] = "contacts[{$index}].last_name is required";
                }
                
                if (isset($contact['email'])) {
                    try {
                        Validator::email($contact['email'], "contacts[{$index}].email");
                    } catch (ValidationException $e) {
                        $errors = array_merge($errors, $e->getErrors());
                    }
                }
                
                if (isset($contact['phone'])) {
                    try {
                        Validator::phoneNumber($contact['phone'], "contacts[{$index}].phone");
                    } catch (ValidationException $e) {
                        $errors = array_merge($errors, $e->getErrors());
                    }
                }
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
    }
    
    /**
     * Set the API client for this organization (required for sending)
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
     * Set the organization name
     *
     * @param string $name The organization name
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setName(string $name): self
    {
        Validator::string($name, 'Name');
        Validator::notEmpty($name, 'Name');
        Validator::maxLength($name, 255, 'Name');
        
        return $this->set('name', $name);
    }
    
    /**
     * Set the address
     *
     * @param Address $address The address
     * @return $this
     */
    public function setAddress(Address $address): self
    {
        $this->address = $address;
        return $this->set('address', $address->toArray());
    }
    
    /**
     * Set the postal code
     *
     * @param string $postalCode The postal code
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setPostalCode(string $postalCode): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set postal code on organization without an address. Create an address first.");
        }
        
        $this->address->setPostalCode($postalCode);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the locality (city/town)
     *
     * @param string $locality The locality
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setLocality(string $locality): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set locality on organization without an address. Create an address first.");
        }
        
        $this->address->setLocality($locality);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the country code
     *
     * @param string $country The country code (ISO 3166-1 alpha-2)
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setCountry(string $country): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set country on organization without an address. Create an address first.");
        }
        
        $this->address->setCountry($country);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the street
     *
     * @param string|null $street The street or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setStreet(?string $street): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set street on organization without an address. Create an address first.");
        }
        
        $this->address->setStreet($street);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the house number
     *
     * @param string|null $houseNumber The house number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setHouseNumber(?string $houseNumber): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set house number on organization without an address. Create an address first.");
        }
        
        $this->address->setHouseNumber($houseNumber);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the email
     *
     * @param string|null $email The email address or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setEmail(?string $email): self
    {
        if ($email === null) {
            return $this->remove('email');
        }
        
        Validator::string($email, 'Email');
        Validator::email($email, 'Email');
        
        return $this->set('email', $email);
    }
    
    /**
     * Set the phone number
     *
     * @param string|null $phone The phone number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setPhone(?string $phone): self
    {
        if ($phone === null) {
            return $this->remove('phone');
        }
        
        Validator::string($phone, 'Phone');
        Validator::phoneNumber($phone, 'Phone');
        
        return $this->set('phone', $phone);
    }
    
    /**
     * Set the website
     *
     * @param string|null $website The website URL or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setWebsite(?string $website): self
    {
        if ($website === null) {
            return $this->remove('website');
        }
        
        Validator::string($website, 'Website');
        Validator::url($website, 'Website');
        
        return $this->set('website', $website);
    }
    
    /**
     * Set the VAT ID
     *
     * @param string|null $vatId The VAT ID or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setVatId(?string $vatId): self
    {
        if ($vatId === null) {
            return $this->remove('vat_id');
        }
        
        Validator::string($vatId, 'VAT ID');
        Validator::maxLength($vatId, 50, 'VAT ID');
        
        return $this->set('vat_id', $vatId);
    }
    
    /**
     * Set the tax ID
     *
     * @param string|null $taxId The tax ID or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setTaxId(?string $taxId): self
    {
        if ($taxId === null) {
            return $this->remove('tax_id');
        }
        
        Validator::string($taxId, 'Tax ID');
        Validator::maxLength($taxId, 50, 'Tax ID');
        
        return $this->set('tax_id', $taxId);
    }
    
    /**
     * Add a contact to the organization
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param array $additionalData Additional contact data
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function addContact(string $firstName, string $lastName, array $additionalData = []): self
    {
        $contact = Contact::create($firstName, $lastName);
        
        // Set additional data using setters when available
        foreach ($additionalData as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($contact, $method)) {
                $contact->$method($value);
            } else {
                $contact->setCustomField($key, $value);
            }
        }
        
        // Validate the contact
        $contact->validate();
        
        // Add the contact to the organization
        if (!isset($this->data['contacts'])) {
            $this->data['contacts'] = [];
        }
        
        $this->data['contacts'][] = $contact->toArray();
        
        return $this;
    }
    
    /**
     * Set a custom field in the organization data
     *
     * @param string $key The field key
     * @param mixed $value The field value or null to remove
     * @return $this
     */
    public function setCustomField(string $key, $value = null): self
    {
        if ($value === null) {
            return $this->remove($key);
        }
        
        $this->customData[$key] = $value;
        return $this->set($key, $value);
    }
    
    /**
     * Set custom data fields in bulk
     *
     * @param array $data The custom data fields
     * @return $this
     */
    public function setCustomData(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->setCustomField($key, $value);
        }
        
        return $this;
    }
    
    /**
     * Get custom data fields
     *
     * @return array
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }
    
    /**
     * Send the organization to the Ameax API
     *
     * @return array The API response
     * @throws ValidationException If validation fails
     * @throws InvalidArgumentException If no API client is set
     */
    public function sendToAmeax(): array
    {
        // Validate the organization before sending
        $this->validate();
        
        if (!$this->apiClient) {
            throw new InvalidArgumentException(
                'No API client set. Use setApiClient() before calling sendToAmeax().'
            );
        }
        
        return $this->apiClient->sendOrganization($this->data);
    }
}
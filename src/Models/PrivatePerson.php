<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;
use InvalidArgumentException;

class PrivatePerson extends BaseModel
{
    public const DOCUMENT_TYPE = 'ameax_private_person_account';
    public const SCHEMA_VERSION = '1.0';
    
    /**
     * @var AmeaxJsonImportApi|null The API client to use for sending
     */
    protected ?AmeaxJsonImportApi $apiClient = null;
    
    /**
     * @var Meta The meta data
     */
    protected Meta $meta;
    
    /**
     * @var Identifiers|null The identifiers
     */
    protected ?Identifiers $identifiers = null;
    
    /**
     * @var Address|null The address
     */
    protected ?Address $address = null;
    
    /**
     * @var Communications|null The communications
     */
    protected ?Communications $communications = null;
    
    /**
     * @var Agent|null The agent
     */
    protected ?Agent $agent = null;
    
    /**
     * @var array Custom data fields
     */
    protected array $customData = [];
    
    /**
     * Constructor initializes a new private person with meta information
     */
    public function __construct()
    {
        $this->meta = new Meta();
        $this->meta->setDocumentType(self::DOCUMENT_TYPE);
        $this->meta->setSchemaVersion(self::SCHEMA_VERSION);
        
        $this->data = [
            'meta' => $this->meta->toArray(),
        ];
        
        $this->customData = [];
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
        // Handle meta data
        if (isset($data['meta']) && is_array($data['meta'])) {
            $metaData = $data['meta'];
            // Ensure correct document_type
            $metaData['document_type'] = self::DOCUMENT_TYPE;
            
            $this->meta = Meta::fromArray($metaData, true);
            $this->data['meta'] = $this->meta->toArray();
        } else {
            // For backward compatibility
            $metaData = [
                'document_type' => self::DOCUMENT_TYPE,
                'schema_version' => self::SCHEMA_VERSION,
            ];
            
            if (isset($data['document_type'])) {
                $metaData['document_type'] = self::DOCUMENT_TYPE; // Always use the correct document type
            }
            
            if (isset($data['schema_version'])) {
                $metaData['schema_version'] = $data['schema_version'];
            }
            
            $this->meta = Meta::fromArray($metaData, true);
            $this->data['meta'] = $this->meta->toArray();
        }
        
        // Handle basic personal information
        if (isset($data['salutation'])) {
            $this->setSalutation($data['salutation']);
        }
        
        if (isset($data['honorifics'])) {
            $this->setHonorifics($data['honorifics']);
        }
        
        if (isset($data['firstname'])) {
            $this->setFirstName($data['firstname']);
        }
        
        if (isset($data['lastname'])) {
            $this->setLastName($data['lastname']);
        }
        
        if (isset($data['date_of_birth'])) {
            $this->setDateOfBirth($data['date_of_birth']);
        }
        
        // Handle nested objects
        if (isset($data['identifiers']) && is_array($data['identifiers'])) {
            $this->identifiers = Identifiers::fromArray($data['identifiers'], true);
            $this->data['identifiers'] = $this->identifiers->toArray();
        } elseif (isset($data['customer_number'])) {
            // For backward compatibility
            $identifiersData = [
                'customer_number' => $data['customer_number']
            ];
            $this->identifiers = Identifiers::fromArray($identifiersData, true);
            $this->data['identifiers'] = $this->identifiers->toArray();
        }
        
        if (isset($data['address']) && is_array($data['address'])) {
            $this->address = Address::fromArray($data['address'], true);
            $this->data['address'] = $this->address->toArray();
        }
        
        if (isset($data['communications']) && is_array($data['communications'])) {
            $this->communications = Communications::fromArray($data['communications'], true);
            $this->data['communications'] = $this->communications->toArray();
        } elseif (isset($data['email']) || isset($data['phone']) || isset($data['mobile']) || isset($data['fax'])) {
            // For backward compatibility
            $communicationsData = [];
            if (isset($data['email'])) {
                $communicationsData['email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $communicationsData['phone_number'] = $data['phone'];
            }
            if (isset($data['mobile'])) {
                $communicationsData['mobile_phone'] = $data['mobile'];
            }
            if (isset($data['fax'])) {
                $communicationsData['fax'] = $data['fax'];
            }
            $this->communications = Communications::fromArray($communicationsData, true);
            $this->data['communications'] = $this->communications->toArray();
        }
        
        if (isset($data['agent']) && is_array($data['agent'])) {
            $this->agent = Agent::fromArray($data['agent'], true);
            $this->data['agent'] = $this->agent->toArray();
        }
        
        // Handle custom data
        if (isset($data['custom_data']) && is_array($data['custom_data'])) {
            $this->customData = $data['custom_data'];
            $this->data['custom_data'] = $this->customData;
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
        
        // Validate meta (always required)
        try {
            $this->meta->validate();
        } catch (ValidationException $e) {
            foreach ($e->getErrors() as $error) {
                $errors[] = "meta: {$error}";
            }
        }
        
        // Required fields
        if (!$this->has('firstname')) {
            $errors[] = "firstname is required";
        }
        
        if (!$this->has('lastname')) {
            $errors[] = "lastname is required";
        }
        
        if (!$this->has('address')) {
            $errors[] = "address is required";
        } elseif (is_array($this->get('address'))) {
            try {
                $this->address->validate();
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $error) {
                    $errors[] = "address: {$error}";
                }
            }
        }
        
        // Validate salutation if present
        if ($this->has('salutation') && $this->get('salutation') !== null) {
            $validSalutations = ['Mr.', 'Ms.', 'Mx.'];
            if (!in_array($this->get('salutation'), $validSalutations)) {
                $errors[] = "Salutation must be one of: " . implode(', ', $validSalutations);
            }
        }
        
        // Validate date of birth if present
        if ($this->has('date_of_birth') && $this->get('date_of_birth') !== null) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->get('date_of_birth'))) {
                $errors[] = "Date of birth must be in format YYYY-MM-DD";
            }
        }
        
        // Validate optional nested objects if present
        if ($this->identifiers !== null) {
            try {
                $this->identifiers->validate();
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $error) {
                    $errors[] = "identifiers: {$error}";
                }
            }
        }
        
        if ($this->communications !== null) {
            try {
                $this->communications->validate();
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $error) {
                    $errors[] = "communications: {$error}";
                }
            }
        }
        
        if ($this->agent !== null) {
            try {
                $this->agent->validate();
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $error) {
                    $errors[] = "agent: {$error}";
                }
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
    }
    
    /**
     * Set the API client for this private person (required for sending)
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
     * Set the meta object
     *
     * @param Meta $meta The meta data
     * @return $this
     */
    public function setMeta(Meta $meta): self
    {
        // Ensure correct document_type
        $meta->setDocumentType(self::DOCUMENT_TYPE);
        
        $this->meta = $meta;
        return $this->set('meta', $meta->toArray());
    }
    
    /**
     * Set the schema version
     *
     * @param string $version The schema version
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setSchemaVersion(string $version): self
    {
        $this->meta->setSchemaVersion($version);
        return $this->set('meta', $this->meta->toArray());
    }
    
    /**
     * Set the salutation
     *
     * @param string|null $salutation The salutation (Mr., Ms., Mx.) or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setSalutation(?string $salutation): self
    {
        if ($salutation === null) {
            return $this->set('salutation', null);
        }
        
        $validSalutations = ['Mr.', 'Ms.', 'Mx.'];
        if (!in_array($salutation, $validSalutations)) {
            throw new ValidationException(["Salutation must be one of: " . implode(', ', $validSalutations)]);
        }
        
        return $this->set('salutation', $salutation);
    }
    
    /**
     * Set the honorifics
     *
     * @param string|null $honorifics The honorifics (e.g., Dr., Prof.) or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setHonorifics(?string $honorifics): self
    {
        if ($honorifics === null) {
            return $this->set('honorifics', null);
        }
        
        Validator::string($honorifics, 'Honorifics');
        Validator::maxLength($honorifics, 50, 'Honorifics');
        
        return $this->set('honorifics', $honorifics);
    }
    
    /**
     * Set the first name
     *
     * @param string $firstName The first name
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setFirstName(string $firstName): self
    {
        Validator::string($firstName, 'First name');
        Validator::notEmpty($firstName, 'First name');
        Validator::maxLength($firstName, 255, 'First name');
        
        return $this->set('firstname', $firstName);
    }
    
    /**
     * Set the last name
     *
     * @param string $lastName The last name
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setLastName(string $lastName): self
    {
        Validator::string($lastName, 'Last name');
        Validator::notEmpty($lastName, 'Last name');
        Validator::maxLength($lastName, 255, 'Last name');
        
        return $this->set('lastname', $lastName);
    }
    
    /**
     * Set the date of birth
     *
     * @param string|null $dateOfBirth The date of birth (format: YYYY-MM-DD) or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setDateOfBirth(?string $dateOfBirth): self
    {
        if ($dateOfBirth === null) {
            return $this->set('date_of_birth', null);
        }
        
        Validator::string($dateOfBirth, 'Date of birth');
        
        // Validate date format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
            throw new ValidationException(["Date of birth must be in format YYYY-MM-DD"]);
        }
        
        return $this->set('date_of_birth', $dateOfBirth);
    }
    
    /**
     * Create and set identifiers
     *
     * @param string|null $customerNumber The customer number
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function createIdentifiers(?string $customerNumber = null): self
    {
        $identifiers = new Identifiers();
        
        if ($customerNumber !== null) {
            $identifiers->setCustomerNumber($customerNumber);
        }
        
        $this->identifiers = $identifiers;
        return $this->set('identifiers', $identifiers->toArray());
    }
    
    /**
     * Set the identifiers
     *
     * @param Identifiers $identifiers The identifiers
     * @return $this
     */
    public function setIdentifiers(Identifiers $identifiers): self
    {
        $this->identifiers = $identifiers;
        return $this->set('identifiers', $identifiers->toArray());
    }
    
    /**
     * Set the customer number
     *
     * @param string|int|null $customerNumber The customer number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setCustomerNumber($customerNumber): self
    {
        if ($this->identifiers === null) {
            if ($customerNumber === null) {
                return $this;
            }
            
            return $this->createIdentifiers($customerNumber);
        }
        
        $this->identifiers->setCustomerNumber($customerNumber);
        return $this->set('identifiers', $this->identifiers->toArray());
    }
    
    /**
     * Create and set a new address from components
     *
     * @param string $postalCode The postal code
     * @param string $locality The city/town
     * @param string $country The country code (ISO 3166-1 alpha-2)
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function createAddress(string $postalCode, string $locality, string $country): self
    {
        $address = new Address();
        $address->setPostalCode($postalCode)
                ->setLocality($locality)
                ->setCountry($country);
        
        $this->address = $address;
        return $this->set('address', $address->toArray());
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
            throw new InvalidArgumentException("Cannot set postal code without an address. Create an address first.");
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
            throw new InvalidArgumentException("Cannot set locality without an address. Create an address first.");
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
            throw new InvalidArgumentException("Cannot set country without an address. Create an address first.");
        }
        
        $this->address->setCountry($country);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the route (street)
     *
     * @param string|null $route The route/street or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setRoute(?string $route): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set route without an address. Create an address first.");
        }
        
        $this->address->setRoute($route);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Set the street (alias for setRoute)
     *
     * @param string|null $street The street or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setStreet(?string $street): self
    {
        return $this->setRoute($street);
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
            throw new InvalidArgumentException("Cannot set house number without an address. Create an address first.");
        }
        
        $this->address->setHouseNumber($houseNumber);
        return $this->set('address', $this->address->toArray());
    }
    
    /**
     * Create and set communications
     *
     * @param string|null $email The email address
     * @param string|null $phoneNumber The phone number
     * @param string|null $mobilePhone The mobile phone number
     * @param string|null $fax The fax number
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function createCommunications(
        ?string $email = null,
        ?string $phoneNumber = null,
        ?string $mobilePhone = null,
        ?string $fax = null
    ): self {
        $communications = new Communications();
        
        if ($email !== null) {
            $communications->setEmail($email);
        }
        
        if ($phoneNumber !== null) {
            $communications->setPhoneNumber($phoneNumber);
        }
        
        if ($mobilePhone !== null) {
            $communications->setMobilePhone($mobilePhone);
        }
        
        if ($fax !== null) {
            $communications->setFax($fax);
        }
        
        $this->communications = $communications;
        return $this->set('communications', $communications->toArray());
    }
    
    /**
     * Set the communications
     *
     * @param Communications $communications The communications
     * @return $this
     */
    public function setCommunications(Communications $communications): self
    {
        $this->communications = $communications;
        return $this->set('communications', $communications->toArray());
    }
    
    /**
     * Set the email (creates communications if needed)
     *
     * @param string|null $email The email address or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setEmail(?string $email): self
    {
        if ($this->communications === null) {
            if ($email === null) {
                return $this;
            }
            
            return $this->createCommunications($email);
        }
        
        $this->communications->setEmail($email);
        return $this->set('communications', $this->communications->toArray());
    }
    
    /**
     * Set the phone number (creates communications if needed)
     *
     * @param string|null $phoneNumber The phone number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setPhone(?string $phoneNumber): self
    {
        if ($this->communications === null) {
            if ($phoneNumber === null) {
                return $this;
            }
            
            return $this->createCommunications(null, $phoneNumber);
        }
        
        $this->communications->setPhoneNumber($phoneNumber);
        return $this->set('communications', $this->communications->toArray());
    }
    
    /**
     * Set the mobile phone number (creates communications if needed)
     *
     * @param string|null $mobilePhone The mobile phone number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setMobilePhone(?string $mobilePhone): self
    {
        if ($this->communications === null) {
            if ($mobilePhone === null) {
                return $this;
            }
            
            return $this->createCommunications(null, null, $mobilePhone);
        }
        
        $this->communications->setMobilePhone($mobilePhone);
        return $this->set('communications', $this->communications->toArray());
    }
    
    /**
     * Set the fax number (creates communications if needed)
     *
     * @param string|null $fax The fax number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setFax(?string $fax): self
    {
        if ($this->communications === null) {
            if ($fax === null) {
                return $this;
            }
            
            return $this->createCommunications(null, null, null, $fax);
        }
        
        $this->communications->setFax($fax);
        return $this->set('communications', $this->communications->toArray());
    }
    
    /**
     * Create and set agent
     *
     * @param string|int|null $externalId The external ID
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function createAgent($externalId = null): self
    {
        $agent = new Agent();
        
        if ($externalId !== null) {
            $agent->setExternalId($externalId);
        }
        
        $this->agent = $agent;
        return $this->set('agent', $agent->toArray());
    }
    
    /**
     * Set the agent
     *
     * @param Agent $agent The agent
     * @return $this
     */
    public function setAgent(Agent $agent): self
    {
        $this->agent = $agent;
        return $this->set('agent', $agent->toArray());
    }
    
    /**
     * Set the agent's external ID (creates agent if needed)
     *
     * @param string|int|null $externalId The external ID or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setAgentExternalId($externalId): self
    {
        if ($this->agent === null) {
            if ($externalId === null) {
                return $this;
            }
            
            return $this->createAgent($externalId);
        }
        
        $this->agent->setExternalId($externalId);
        return $this->set('agent', $this->agent->toArray());
    }
    
    /**
     * Set a custom data field
     *
     * @param string $key The field key
     * @param mixed $value The field value or null to remove
     * @return $this
     */
    public function setCustomField(string $key, $value = null): self
    {
        if (!isset($this->data['custom_data'])) {
            $this->data['custom_data'] = [];
        }
        
        if ($value === null) {
            unset($this->customData[$key]);
            unset($this->data['custom_data'][$key]);
            
            if (empty($this->customData)) {
                $this->remove('custom_data');
            }
            
            return $this;
        }
        
        $this->customData[$key] = $value;
        $this->data['custom_data'][$key] = $value;
        
        return $this;
    }
    
    /**
     * Set custom data fields in bulk
     *
     * @param array $data The custom data fields
     * @return $this
     */
    public function setCustomData(array $data): self
    {
        $this->customData = $data;
        $this->data['custom_data'] = $data;
        
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
     * Get the document type
     *
     * @return string
     */
    public function getDocumentType(): string
    {
        return $this->meta->getDocumentType();
    }
    
    /**
     * Get the schema version
     *
     * @return string
     */
    public function getSchemaVersion(): string
    {
        return $this->meta->getSchemaVersion();
    }
    
    /**
     * Get the meta data
     *
     * @return Meta
     */
    public function getMeta(): Meta
    {
        return $this->meta;
    }
    
    /**
     * Get the salutation
     *
     * @return string|null
     */
    public function getSalutation(): ?string
    {
        return $this->get('salutation');
    }
    
    /**
     * Get the honorifics
     *
     * @return string|null
     */
    public function getHonorifics(): ?string
    {
        return $this->get('honorifics');
    }
    
    /**
     * Get the first name
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->get('firstname');
    }
    
    /**
     * Get the last name
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->get('lastname');
    }
    
    /**
     * Get the date of birth
     *
     * @return string|null
     */
    public function getDateOfBirth(): ?string
    {
        return $this->get('date_of_birth');
    }
    
    /**
     * Get the identifiers
     *
     * @return Identifiers|null
     */
    public function getIdentifiers(): ?Identifiers
    {
        return $this->identifiers;
    }
    
    /**
     * Get the customer number
     *
     * @return string|int|null
     */
    public function getCustomerNumber()
    {
        return $this->identifiers ? $this->identifiers->getCustomerNumber() : null;
    }
    
    /**
     * Get the address
     *
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }
    
    /**
     * Get the communications
     *
     * @return Communications|null
     */
    public function getCommunications(): ?Communications
    {
        return $this->communications;
    }
    
    /**
     * Get the email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->communications ? $this->communications->getEmail() : null;
    }
    
    /**
     * Get the phone number
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->communications ? $this->communications->getPhoneNumber() : null;
    }
    
    /**
     * Get the mobile phone number
     *
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->communications ? $this->communications->getMobilePhone() : null;
    }
    
    /**
     * Get the fax number
     *
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->communications ? $this->communications->getFax() : null;
    }
    
    /**
     * Get the agent
     *
     * @return Agent|null
     */
    public function getAgent(): ?Agent
    {
        return $this->agent;
    }
    
    /**
     * Send the private person to the Ameax API
     *
     * @return array The API response
     * @throws ValidationException If validation fails
     * @throws InvalidArgumentException If no API client is set
     */
    public function sendToAmeax(): array
    {
        // Validate the private person before sending
        $this->validate();
        
        if (!$this->apiClient) {
            throw new InvalidArgumentException(
                'No API client set. Use setApiClient() before calling sendToAmeax().'
            );
        }
        
        return $this->apiClient->sendPrivatePerson($this->data);
    }
}
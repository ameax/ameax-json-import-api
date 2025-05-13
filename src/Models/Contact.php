<?php

namespace Ameax\AmeaxJsonImportApi\Models;




class Contact extends BaseModel
{
    /**
     * @var Identifiers|null The contact identifiers
     */
    protected ?Identifiers $identifiers = null;
    
    /**
     * @var Employment|null The contact employment info
     */
    protected ?Employment $employment = null;
    
    /**
     * @var Communications|null The contact communications
     */
    protected ?Communications $communications = null;
    
    /**
     * @var array Custom data fields
     */
    protected array $customData = [];
    
    /**
     * Constructor initializes an empty contact
     */
    public function __construct()
    {
        $this->data = [];
        $this->customData = [];
    }
    
    /**
     * Populate the model with data using setters
     *
     * @param array $data
     * @return $this
     * 
     */
    protected function populate(array $data): self
    {
        // Handle standard fields
        if (isset($data['salutation'])) {
            $this->setSalutation($data['salutation']);
        }
        
        if (isset($data['honorifics'])) {
            $this->setHonorifics($data['honorifics']);
        }
        
        if (isset($data['firstname']) || isset($data['first_name'])) {
            $firstname = $data['firstname'] ?? $data['first_name'];
            $this->setFirstName($firstname);
        }
        
        if (isset($data['lastname']) || isset($data['last_name'])) {
            $lastname = $data['lastname'] ?? $data['last_name'];
            $this->setLastName($lastname);
        }
        
        if (isset($data['date_of_birth'])) {
            $this->setDateOfBirth($data['date_of_birth']);
        }
        
        // Handle nested objects
        if (isset($data['identifiers']) && is_array($data['identifiers'])) {
            $this->identifiers = Identifiers::fromArray($data['identifiers']);
            $this->data['identifiers'] = $this->identifiers->toArray();
        }
        
        if (isset($data['employment']) && is_array($data['employment'])) {
            $this->employment = Employment::fromArray($data['employment']);
            $this->data['employment'] = $this->employment->toArray();
        } elseif (isset($data['job_title']) || isset($data['department'])) {
            // For backward compatibility
            $employmentData = [];
            if (isset($data['job_title'])) {
                $employmentData['job_title'] = $data['job_title'];
            }
            if (isset($data['department'])) {
                $employmentData['department'] = $data['department'];
            }
            $this->employment = Employment::fromArray($employmentData);
            $this->data['employment'] = $this->employment->toArray();
        }
        
        if (isset($data['communications']) && is_array($data['communications'])) {
            $this->communications = Communications::fromArray($data['communications']);
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
            $this->communications = Communications::fromArray($communicationsData);
            $this->data['communications'] = $this->communications->toArray();
        }
        
        // Handle custom data
        if (isset($data['custom_data']) && is_array($data['custom_data'])) {
            $this->customData = $data['custom_data'];
            $this->data['custom_data'] = $this->customData;
        }
        
        return $this;
    }
    
    
    
    /**
     * Set the salutation
     * 
     * The API accepts the following salutation values: 'Mr.', 'Ms.', 'Mx.'
     * Other values are accepted but may not validate on the server.
     *
     * @param string|null $salutation The salutation or null to remove
     * @return $this
     */
    public function setSalutation(?string $salutation): self
    {
        if ($salutation === null) {
            return $this->set('salutation', null);
        }
        
        // Convert common variations to standardized format
        if (in_array(strtolower(trim($salutation)), ['mr', 'mister'])) {
            $salutation = 'Mr.';
        } else if (in_array(strtolower(trim($salutation)), ['ms', 'miss', 'mrs'])) {
            $salutation = 'Ms.';
        } else if (in_array(strtolower(trim($salutation)), ['mx'])) {
            $salutation = 'Mx.';
        }
        
        return $this->set('salutation', $salutation);
    }
    
    /**
     * Set the honorifics
     *
     * @param string|null $honorifics The honorifics or null to remove
     * @return $this
     * 
     */
    public function setHonorifics(?string $honorifics): self
    {
        if ($honorifics === null) {
            return $this->set('honorifics', null);
        }
        
        return $this->set('honorifics', $honorifics);
    }
    
    /**
     * Set the first name
     *
     * @param string $firstName The first name
     * @return $this
     * 
     */
    public function setFirstName(string $firstName): self
    {
        
        return $this->set('firstname', $firstName);
    }
    
    /**
     * Set the last name
     *
     * @param string $lastName The last name
     * @return $this
     * 
     */
    public function setLastName(string $lastName): self
    {
        
        return $this->set('lastname', $lastName);
    }
    
    /**
     * Set the date of birth
     *
     * @param string|null $dateOfBirth The date of birth (format: YYYY-MM-DD) or null to remove
     * @return $this
     * 
     */
    /**
     * Set the date of birth
     *
     * @param string|\DateTime|mixed|null $dateOfBirth The date of birth (will be converted to YYYY-MM-DD format) or null to remove
     * @return $this
     */
    public function setDateOfBirth($dateOfBirth): self
    {
        if ($dateOfBirth === null) {
            return $this->set('date_of_birth', null);
        }
        
        // If DateTime object provided, convert to string
        if ($dateOfBirth instanceof \DateTime) {
            $dateOfBirth = $dateOfBirth->format('Y-m-d');
        } else if (is_string($dateOfBirth) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
            // Try to parse the date string if it's not in ISO format
            try {
                $date = new \DateTime($dateOfBirth);
                $dateOfBirth = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // If we can't parse it, just pass it through - API will validate it
            }
        }
        
        return $this->set('date_of_birth', $dateOfBirth);
    }
    
    /**
     * Create and set identifiers
     *
     * @param string|int|null $externalId The external ID
     * @return $this
     * 
     */
    public function createIdentifiers($externalId = null): self
    {
        $identifiers = new Identifiers();
        
        if ($externalId !== null) {
            $identifiers->setExternalId($externalId);
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
     * Set the external ID
     *
     * @param string|int|null $externalId The external ID or null to remove
     * @return $this
     * 
     */
    public function setExternalId($externalId): self
    {
        if ($this->identifiers === null) {
            $this->createIdentifiers($externalId);
            return $this;
        }
        
        $this->identifiers->setExternalId($externalId);
        return $this->set('identifiers', $this->identifiers->toArray());
    }
    
    /**
     * Create and set employment information
     *
     * @param string|null $jobTitle The job title
     * @param string|null $department The department
     * @return $this
     * 
     */
    public function createEmployment(?string $jobTitle = null, ?string $department = null): self
    {
        $employment = new Employment();
        
        if ($jobTitle !== null) {
            $employment->setJobTitle($jobTitle);
        }
        
        if ($department !== null) {
            $employment->setDepartment($department);
        }
        
        $this->employment = $employment;
        return $this->set('employment', $employment->toArray());
    }
    
    /**
     * Set the employment
     *
     * @param Employment $employment The employment
     * @return $this
     */
    public function setEmployment(Employment $employment): self
    {
        $this->employment = $employment;
        return $this->set('employment', $employment->toArray());
    }
    
    /**
     * Set the job title (creates employment if needed)
     *
     * @param string|null $jobTitle The job title or null to remove
     * @return $this
     * 
     */
    public function setJobTitle(?string $jobTitle): self
    {
        if ($this->employment === null) {
            return $this->createEmployment($jobTitle);
        }
        
        $this->employment->setJobTitle($jobTitle);
        return $this->set('employment', $this->employment->toArray());
    }
    
    /**
     * Set the department (creates employment if needed)
     *
     * @param string|null $department The department or null to remove
     * @return $this
     * 
     */
    public function setDepartment(?string $department): self
    {
        if ($this->employment === null) {
            return $this->createEmployment(null, $department);
        }
        
        $this->employment->setDepartment($department);
        return $this->set('employment', $this->employment->toArray());
    }
    
    /**
     * Create and set communications
     *
     * @param string|null $email The email address
     * @param string|null $phoneNumber The phone number
     * @param string|null $mobilePhone The mobile phone number
     * @param string|null $fax The fax number
     * @return $this
     * 
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
     * 
     */
    public function setEmail(?string $email): self
    {
        if ($this->communications === null) {
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
     * 
     */
    public function setPhone(?string $phoneNumber): self
    {
        if ($this->communications === null) {
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
     * 
     */
    public function setMobile(?string $mobilePhone): self
    {
        if ($this->communications === null) {
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
     * 
     */
    public function setFax(?string $fax): self
    {
        if ($this->communications === null) {
            return $this->createCommunications(null, null, null, $fax);
        }
        
        $this->communications->setFax($fax);
        return $this->set('communications', $this->communications->toArray());
    }
    
    /**
     * Set a custom data field
     *
     * @param string $key The field key
     * @param mixed $value The field value
     * @return $this
     */
    /**
     * Set a custom data field
     *
     * @param string $key The field key
     * @param mixed $value The field value
     * @return $this
     */
    public function setCustomField(string $key, $value): self
    {
        if (!isset($this->data['custom_data'])) {
            $this->data['custom_data'] = [];
        }
        
        // Type casting for common types
        if ($value === 'true' || $value === 'TRUE' || $value === '1') {
            $value = true;
        } else if ($value === 'false' || $value === 'FALSE' || $value === '0') {
            $value = false;
        } else if (is_string($value) && is_numeric($value) && strpos($value, '.') === false) {
            // Convert string integers to actual integers
            $value = (int)$value;
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
     * Get the custom data
     *
     * @return array
     */
    public function getCustomData(): array
    {
        return $this->customData;
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
     * Get the employment
     *
     * @return Employment|null
     */
    public function getEmployment(): ?Employment
    {
        return $this->employment;
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
}
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
     * @return $this
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
        } elseif (isset($data['email']) || isset($data['phone']) || isset($data['phone_number2']) || isset($data['mobile']) || isset($data['fax'])) {
            // For backward compatibility
            $communicationsData = [];
            if (isset($data['email'])) {
                $communicationsData['email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $communicationsData['phone_number'] = $data['phone'];
            }
            if (isset($data['phone_number2'])) {
                $communicationsData['phone_number2'] = $data['phone_number2'];
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
     * Common values: 'Mr.', 'Ms.', 'Mx.'
     * Supports English and German variations and extraction from combined strings
     *
     * @param  string|null  $salutation  The salutation or null to remove
     * @return $this
     */
    public function setSalutation(?string $salutation): self
    {
        if ($salutation === null || trim($salutation) === '') {
            return $this->set('salutation', null);
        }

        $trimmedInput = trim($salutation);
        $lowerInput = strtolower($trimmedInput);

        // Direct mapping of salutations
        $mrVariations = ['mr', 'mr.', 'mister', 'sir', 'herr', 'herrn', 'hr', 'hr.'];
        $msVariations = ['ms', 'ms.', 'miss', 'mrs', 'mrs.', 'madam', 'frau', 'fr', 'fr.', 'fräulein'];
        $mxVariations = ['mx', 'mx.'];

        // Special handling for cases like "Mr. Dr." where we need to extract honorifics
        $patternMr = '/^(mr\.|mr|herr|herrn|hr\.?)\s+(.+)/i';
        $patternMs = '/^(ms\.|ms|miss|mrs\.?|frau|fr\.?|fräulein)\s+(.+)/i';
        $patternMx = '/^(mx\.?)\s+(.+)/i';

        // Test for combined strings first (salutation + honorific)
        if (preg_match($patternMr, $trimmedInput, $matches)) {
            if (! empty($matches[2])) {
                $this->setHonorifics($matches[2]);
            }

            return $this->set('salutation', 'Mr.');
        }

        if (preg_match($patternMs, $trimmedInput, $matches)) {
            if (! empty($matches[2])) {
                $this->setHonorifics($matches[2]);
            }

            return $this->set('salutation', 'Ms.');
        }

        if (preg_match($patternMx, $trimmedInput, $matches)) {
            if (! empty($matches[2])) {
                $this->setHonorifics($matches[2]);
            }

            return $this->set('salutation', 'Mx.');
        }

        // If no combined string, check for exact matches
        if (in_array($lowerInput, $mrVariations)) {
            return $this->set('salutation', 'Mr.');
        }

        if (in_array($lowerInput, $msVariations)) {
            return $this->set('salutation', 'Ms.');
        }

        if (in_array($lowerInput, $mxVariations)) {
            return $this->set('salutation', 'Mx.');
        }

        // If we couldn't map to a standard value, use the original input
        return $this->set('salutation', $trimmedInput);
    }

    /**
     * Set the honorifics
     *
     * @param  string|null  $honorifics  The honorifics or null to remove
     * @return $this
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
     * @param  string  $firstName  The first name
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {

        return $this->set('firstname', $firstName);
    }

    /**
     * Set the last name
     *
     * @param  string  $lastName  The last name
     * @return $this
     */
    public function setLastName(string $lastName): self
    {

        return $this->set('lastname', $lastName);
    }

    /**
     * Set the date of birth
     *
     * @param  string|\DateTime|mixed|null  $dateOfBirth  The date of birth (will be converted to YYYY-MM-DD format) or null to remove
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
        } elseif (is_string($dateOfBirth) && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
            // Try to parse the date string if it's not in ISO format
            try {
                $date = new \DateTime($dateOfBirth);
                $dateOfBirth = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // If we can't parse it, just pass it through
            }
        }

        return $this->set('date_of_birth', $dateOfBirth);
    }

    /**
     * Create and set identifiers
     *
     * @param  string|int|null  $externalId  The external ID
     * @return $this
     */
    public function createIdentifiers($externalId = null): self
    {
        $identifiers = new Identifiers;

        if ($externalId !== null) {
            $identifiers->setExternalId($externalId);
        }

        $this->identifiers = $identifiers;

        return $this->set('identifiers', $identifiers->toArray());
    }

    /**
     * Set the identifiers
     *
     * @param  Identifiers  $identifiers  The identifiers
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
     * @param  string|int|null  $externalId  The external ID or null to remove
     * @return $this
     */
    public function setExternalId(string|int|null $externalId): self
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
     * @param  string|null  $jobTitle  The job title
     * @param  string|null  $department  The department
     * @return $this
     */
    public function createEmployment(?string $jobTitle = null, ?string $department = null): self
    {
        $employment = new Employment;

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
     * @param  Employment  $employment  The employment
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
     * @param  string|null  $jobTitle  The job title or null to remove
     * @return $this
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
     * @param  string|null  $department  The department or null to remove
     * @return $this
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
     * @param  string|null  $email  The email address
     * @param  string|null  $phoneNumber  The phone number
     * @param  string|null  $phoneNumberTwo  The second phone number
     * @param  string|null  $mobilePhone  The mobile phone number
     * @param  string|null  $fax  The fax number
     * @return $this
     */
    public function createCommunications(
        ?string $email = null,
        ?string $phoneNumber = null,
        ?string $phoneNumberTwo = null,
        ?string $mobilePhone = null,
        ?string $fax = null
    ): self {
        $communications = new Communications;

        if ($email !== null) {
            $communications->setEmail($email);
        }

        if ($phoneNumber !== null) {
            $communications->setPhoneNumber($phoneNumber);
        }

        if ($phoneNumberTwo !== null) {
            $communications->setPhoneNumberTwo($phoneNumberTwo);
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
     * @param  Communications  $communications  The communications
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
     * @param  string|null  $email  The email address or null to remove
     * @return $this
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
     * @param  string|null  $phoneNumber  The phone number or null to remove
     * @return $this
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
     * Set the second phone number (creates communications if needed)
     *
     * @param  string|null  $phoneNumberTwo  The second phone number or null to remove
     * @return $this
     */
    public function setPhoneTwo(?string $phoneNumberTwo): self
    {
        if ($this->communications === null) {
            return $this->createCommunications(null, null, $phoneNumberTwo);
        }

        $this->communications->setPhoneNumberTwo($phoneNumberTwo);

        return $this->set('communications', $this->communications->toArray());
    }

    /**
     * Set the mobile phone number (creates communications if needed)
     *
     * @param  string|null  $mobilePhone  The mobile phone number or null to remove
     * @return $this
     */
    public function setMobilePhone(?string $mobilePhone): self
    {
        if ($this->communications === null) {
            return $this->createCommunications(null, null, $mobilePhone);
        }

        $this->communications->setMobilePhone($mobilePhone);

        return $this->set('communications', $this->communications->toArray());
    }

    /**
     * Set the mobile phone number (alias for setMobilePhone)
     *
     * @param  string|null  $mobilePhone  The mobile phone number or null to remove
     * @return $this
     */
    public function setMobile(?string $mobilePhone): self
    {
        return $this->setMobilePhone($mobilePhone);
    }

    /**
     * Set the fax number (creates communications if needed)
     *
     * @param  string|null  $fax  The fax number or null to remove
     * @return $this
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
     * @param  string  $key  The field key
     * @param  mixed  $value  The field value or null to remove
     * @return $this
     */
    public function setCustomField(string $key, $value = null): self
    {
        if (! isset($this->data['custom_data'])) {
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

        // Type casting for common types
        if ($value === 'true' || $value === 'TRUE' || $value === '1') {
            $value = true;
        } elseif ($value === 'false' || $value === 'FALSE' || $value === '0') {
            $value = false;
        } elseif (is_string($value) && is_numeric($value) && strpos($value, '.') === false) {
            // Convert string integers to actual integers
            $value = (int) $value;
        }

        $this->customData[$key] = $value;
        $this->data['custom_data'][$key] = $value;

        return $this;
    }

    /**
     * Set custom data fields in bulk
     *
     * @param  array  $data  The custom data fields
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
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Get the salutation
     */
    public function getSalutation(): ?string
    {
        return $this->get('salutation');
    }

    /**
     * Get the honorifics
     */
    public function getHonorifics(): ?string
    {
        return $this->get('honorifics');
    }

    /**
     * Get the first name
     */
    public function getFirstName(): ?string
    {
        return $this->get('firstname');
    }

    /**
     * Get the last name
     */
    public function getLastName(): ?string
    {
        return $this->get('lastname');
    }

    /**
     * Get the date of birth
     */
    public function getDateOfBirth(): ?string
    {
        return $this->get('date_of_birth');
    }

    /**
     * Get the identifiers
     */
    public function getIdentifiers(): ?Identifiers
    {
        return $this->identifiers;
    }

    /**
     * Get the employment
     */
    public function getEmployment(): ?Employment
    {
        return $this->employment;
    }

    /**
     * Get the communications
     */
    public function getCommunications(): ?Communications
    {
        return $this->communications;
    }

    /**
     * Get the email
     */
    public function getEmail(): ?string
    {
        return $this->communications ? $this->communications->getEmail() : null;
    }

    /**
     * Get the phone number
     */
    public function getPhone(): ?string
    {
        return $this->communications ? $this->communications->getPhoneNumber() : null;
    }

    /**
     * Get the second phone number
     */
    public function getPhoneNumberTwo(): ?string
    {
        return $this->communications ? $this->communications->getPhoneNumberTwo() : null;
    }

    /**
     * Get the second phone number (alias for getPhoneNumberTwo)
     */
    public function getPhoneTwo(): ?string
    {
        return $this->getPhoneNumberTwo();
    }

    /**
     * Get the mobile phone number
     */
    public function getMobilePhone(): ?string
    {
        return $this->communications ? $this->communications->getMobilePhone() : null;
    }

    /**
     * Get the fax number
     */
    public function getFax(): ?string
    {
        return $this->communications ? $this->communications->getFax() : null;
    }
}

<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
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
     * @return $this
     */
    protected function populate(array $data): self
    {
        // Handle meta data
        if (isset($data['meta']) && is_array($data['meta'])) {
            $metaData = $data['meta'];
            // Ensure correct document_type
            $metaData['document_type'] = self::DOCUMENT_TYPE;

            $this->meta = Meta::fromArray($metaData);
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

            $this->meta = Meta::fromArray($metaData);
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
            $this->identifiers = Identifiers::fromArray($data['identifiers']);
            $this->data['identifiers'] = $this->identifiers->toArray();
        } elseif (isset($data['customer_number']) || isset($data['external_id'])) {
            // For backward compatibility
            $identifiersData = [];
            if (isset($data['customer_number'])) {
                $identifiersData['customer_number'] = $data['customer_number'];
            }
            if (isset($data['external_id'])) {
                $identifiersData['external_id'] = $data['external_id'];
            }
            $this->identifiers = Identifiers::fromArray($identifiersData);
            $this->data['identifiers'] = $this->identifiers->toArray();
        }

        if (isset($data['address']) && is_array($data['address'])) {
            $this->address = Address::fromArray($data['address']);
            $this->data['address'] = $this->address->toArray();
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

        if (isset($data['agent']) && is_array($data['agent'])) {
            $this->agent = Agent::fromArray($data['agent']);
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
     * Set the API client for this private person (required for sending)
     *
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
     * @param  Meta  $meta  The meta data
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
     * @param  string  $version  The schema version
     * @return $this
     */
    public function setSchemaVersion(string $version): self
    {
        $this->meta->setSchemaVersion($version);

        return $this->set('meta', $this->meta->toArray());
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

        $originalInput = $salutation;
        $salutation = trim($salutation);

        // Map of recognized English and German salutations
        $mrVariations = ['mr', 'mr.', 'mister', 'sir', 'herr', 'herrn', 'hr', 'hr.'];
        $msVariations = ['ms', 'ms.', 'miss', 'mrs', 'mrs.', 'madam', 'frau', 'fr', 'fr.', 'fräulein'];
        $mxVariations = ['mx', 'mx.'];

        $lowerSalutation = strtolower($salutation);

        // First check for exact matches
        if (in_array($lowerSalutation, $mrVariations)) {
            return $this->set('salutation', 'Mr.');
        }

        if (in_array($lowerSalutation, $msVariations)) {
            return $this->set('salutation', 'Ms.');
        }

        if (in_array($lowerSalutation, $mxVariations)) {
            return $this->set('salutation', 'Mx.');
        }

        // Now check for combined forms (salutation + honorific)
        // Mr. variations
        foreach ($mrVariations as $prefix) {
            $pattern = '/^'.preg_quote($prefix, '/').'\s+(.+)$/i';
            if (preg_match($pattern, $salutation, $matches)) {
                if (! empty($matches[1])) {
                    $this->setHonorifics($matches[1]);
                }

                return $this->set('salutation', 'Mr.');
            }
        }

        // Ms. variations
        foreach ($msVariations as $prefix) {
            $pattern = '/^'.preg_quote($prefix, '/').'\s+(.+)$/i';
            if (preg_match($pattern, $salutation, $matches)) {
                if (! empty($matches[1])) {
                    $this->setHonorifics($matches[1]);
                }

                return $this->set('salutation', 'Ms.');
            }
        }

        // Mx. variations
        foreach ($mxVariations as $prefix) {
            $pattern = '/^'.preg_quote($prefix, '/').'\s+(.+)$/i';
            if (preg_match($pattern, $salutation, $matches)) {
                if (! empty($matches[1])) {
                    $this->setHonorifics($matches[1]);
                }

                return $this->set('salutation', 'Mx.');
            }
        }

        // If we couldn't map to a standard value, use the original input
        return $this->set('salutation', $originalInput);
    }

    /**
     * Set the honorifics
     *
     * @param  string|null  $honorifics  The honorifics (e.g., Dr., Prof.) or null to remove
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
     * @param  string|null  $customerNumber  The customer number
     * @return $this
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
     * @param  Identifiers  $identifiers  The identifiers
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
     * @param  string|int|null  $customerNumber  The customer number or null to remove
     * @return $this
     */
    public function setCustomerNumber(string|int|null $customerNumber): self
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
     * Set the external ID
     *
     * @param  string|int|null  $externalId  The external ID or null to remove
     * @return $this
     */
    public function setExternalId(string|int|null $externalId): self
    {
        if ($this->identifiers === null) {
            if ($externalId === null) {
                return $this;
            }

            $this->createIdentifiers();
        }

        $this->identifiers->setExternalId($externalId);

        return $this->set('identifiers', $this->identifiers->toArray());
    }

    /**
     * Set the Ameax internal ID
     *
     * @param  int|null  $ameaxInternalId  The Ameax internal ID or null to remove
     * @return $this
     */
    public function setAmeaxInternalId(?int $ameaxInternalId): self
    {
        if ($this->identifiers === null) {
            if ($ameaxInternalId === null) {
                return $this;
            }

            $this->createIdentifiers();
        }

        $this->identifiers->setAmeaxInternalId($ameaxInternalId);

        return $this->set('identifiers', $this->identifiers->toArray());
    }

    /**
     * Create and set a new address from components
     *
     * @param  string  $postalCode  The postal code
     * @param  string  $locality  The city/town
     * @param  string  $country  The country code (ISO 3166-1 alpha-2)
     * @return $this
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
     * @param  Address  $address  The address
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
     * @param  string  $postalCode  The postal code
     * @return $this
     */
    public function setPostalCode(string $postalCode): self
    {
        if (! $this->address) {
            throw new InvalidArgumentException('Cannot set postal code without an address. Create an address first.');
        }

        $this->address->setPostalCode($postalCode);

        return $this->set('address', $this->address->toArray());
    }

    /**
     * Set the locality (city/town)
     *
     * @param  string  $locality  The locality
     * @return $this
     */
    public function setLocality(string $locality): self
    {
        if (! $this->address) {
            throw new InvalidArgumentException('Cannot set locality without an address. Create an address first.');
        }

        $this->address->setLocality($locality);

        return $this->set('address', $this->address->toArray());
    }

    /**
     * Set the country code
     *
     * @param  string  $country  The country code (ISO 3166-1 alpha-2)
     * @return $this
     */
    public function setCountry(string $country): self
    {
        if (! $this->address) {
            throw new InvalidArgumentException('Cannot set country without an address. Create an address first.');
        }

        $this->address->setCountry($country);

        return $this->set('address', $this->address->toArray());
    }

    /**
     * Set the route (street)
     *
     * @param  string|null  $route  The route/street or null to remove
     * @return $this
     */
    public function setRoute(?string $route): self
    {
        if (! $this->address) {
            throw new InvalidArgumentException('Cannot set route without an address. Create an address first.');
        }

        $this->address->setRoute($route);

        return $this->set('address', $this->address->toArray());
    }

    /**
     * Set the street (alias for setRoute)
     *
     * @param  string|null  $street  The street or null to remove
     * @return $this
     */
    public function setStreet(?string $street): self
    {
        return $this->setRoute($street);
    }

    /**
     * Set the house number
     *
     * @param  string|null  $houseNumber  The house number or null to remove
     * @return $this
     */
    public function setHouseNumber(?string $houseNumber): self
    {
        if (! $this->address) {
            throw new InvalidArgumentException('Cannot set house number without an address. Create an address first.');
        }

        $this->address->setHouseNumber($houseNumber);

        return $this->set('address', $this->address->toArray());
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
        $communications = new Communications();

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
     * @param  string|null  $phoneNumber  The phone number or null to remove
     * @return $this
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
     * Set the second phone number (creates communications if needed)
     *
     * @param  string|null  $phoneNumberTwo  The second phone number or null to remove
     * @return $this
     */
    public function setPhoneTwo(?string $phoneNumberTwo): self
    {
        if ($this->communications === null) {
            if ($phoneNumberTwo === null) {
                return $this;
            }

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
     * @param  string|null  $fax  The fax number or null to remove
     * @return $this
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
     * @param  string|int|null  $externalId  The external ID
     * @return $this
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
     * @param  Agent  $agent  The agent
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
     * @param  string|int|null  $externalId  The external ID or null to remove
     * @return $this
     */
    public function setAgentExternalId(string|int|null $externalId): self
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
     * Get custom data fields
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Get the document type
     */
    public function getDocumentType(): string
    {
        return $this->meta->getDocumentType();
    }

    /**
     * Get the schema version
     */
    public function getSchemaVersion(): string
    {
        return $this->meta->getSchemaVersion();
    }

    /**
     * Get the meta data
     */
    public function getMeta(): Meta
    {
        return $this->meta;
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
     * Get the customer number
     */
    public function getCustomerNumber(): ?string
    {
        return $this->identifiers ? $this->identifiers->getCustomerNumber() : null;
    }

    /**
     * Get the external ID
     */
    public function getExternalId(): ?string
    {
        return $this->identifiers ? $this->identifiers->getExternalId() : null;
    }

    /**
     * Get the Ameax internal ID
     */
    public function getAmeaxInternalId(): ?int
    {
        return $this->identifiers ? $this->identifiers->getAmeaxInternalId() : null;
    }

    /**
     * Get the address
     */
    public function getAddress(): ?Address
    {
        return $this->address;
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

    /**
     * Get the agent
     */
    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    /**
     * Send the private person to the Ameax API
     *
     * @return array The API response
     *
     * @throws InvalidArgumentException If no API client is set
     */
    public function sendToAmeax(): array
    {
        if (! $this->apiClient) {
            throw new InvalidArgumentException(
                'No API client set. Use setApiClient() before calling sendToAmeax().'
            );
        }

        return $this->apiClient->sendPrivatePerson($this->data);
    }
}

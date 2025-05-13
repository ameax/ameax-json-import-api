<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use InvalidArgumentException;

class Organization extends BaseModel
{
    /**
     * @var AmeaxJsonImportApi|null The API client to use for sending
     */
    protected ?AmeaxJsonImportApi $apiClient = null;

    /**
     * @var Meta The meta data
     */
    protected Meta $meta;

    /**
     * @var Identifiers|null The organization identifiers
     */
    protected ?Identifiers $identifiers = null;

    /**
     * @var Address|null The organization address
     */
    protected ?Address $address = null;

    /**
     * @var SocialMedia|null The organization social media
     */
    protected ?SocialMedia $socialMedia = null;

    /**
     * @var Communications|null The organization communications
     */
    protected ?Communications $communications = null;

    /**
     * @var BusinessInformation|null The organization business information
     */
    protected ?BusinessInformation $businessInformation = null;

    /**
     * @var Agent|null The organization agent
     */
    protected ?Agent $agent = null;

    /**
     * @var array Custom data fields
     */
    protected array $customData = [];

    /**
     * @var array Contact objects
     */
    protected array $contactObjects = [];

    /**
     * Constructor initializes a new organization with meta information
     */
    public function __construct()
    {
        $this->meta = new Meta(Meta::DOCUMENT_TYPE_ORGANIZATION);

        $this->data = [
            'meta' => $this->meta->toArray(),
        ];

        $this->customData = [];
        $this->contactObjects = [];
    }

    /**
     * Populate the model with data using setters
     *
     * @param array $data
     * @return $this
     */
    protected function populate(array $data): self
    {
        // Handle meta data
        if (isset($data['meta']) && is_array($data['meta'])) {
            $this->meta = Meta::fromArray($data['meta']);
            $this->data['meta'] = $this->meta->toArray();
        } else {
            // For backward compatibility
            $metaData = [
                'document_type' => Meta::DOCUMENT_TYPE_ORGANIZATION,
                'schema_version' => Meta::SCHEMA_VERSION,
            ];

            if (isset($data['document_type'])) {
                $metaData['document_type'] = $data['document_type'];
            }

            if (isset($data['schema_version'])) {
                $metaData['schema_version'] = $data['schema_version'];
            }

            $this->meta = Meta::fromArray($metaData);
            $this->data['meta'] = $this->meta->toArray();
        }

        // Handle standard fields
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }

        if (isset($data['additional_name'])) {
            $this->setAdditionalName($data['additional_name']);
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

        if (isset($data['social_media']) && is_array($data['social_media'])) {
            $this->socialMedia = SocialMedia::fromArray($data['social_media']);
            $this->data['social_media'] = $this->socialMedia->toArray();
        } elseif (isset($data['website'])) {
            // For backward compatibility
            $socialMediaData = [
                'web' => $data['website'],
            ];
            $this->socialMedia = SocialMedia::fromArray($socialMediaData);
            $this->data['social_media'] = $this->socialMedia->toArray();
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

        if (isset($data['business_information']) && is_array($data['business_information'])) {
            $this->businessInformation = BusinessInformation::fromArray($data['business_information']);
            $this->data['business_information'] = $this->businessInformation->toArray();
        } elseif (isset($data['vat_id']) || isset($data['iban'])) {
            // For backward compatibility
            $businessInfoData = [];
            if (isset($data['vat_id'])) {
                $businessInfoData['vat_id'] = $data['vat_id'];
            }
            if (isset($data['iban'])) {
                $businessInfoData['iban'] = $data['iban'];
            }
            $this->businessInformation = BusinessInformation::fromArray($businessInfoData);
            $this->data['business_information'] = $this->businessInformation->toArray();
        }

        if (isset($data['agent']) && is_array($data['agent'])) {
            $this->agent = Agent::fromArray($data['agent']);
            $this->data['agent'] = $this->agent->toArray();
        }

        // Handle contacts
        if (isset($data['contacts']) && is_array($data['contacts'])) {
            $this->data['contacts'] = [];
            $this->contactObjects = [];

            foreach ($data['contacts'] as $contactData) {
                // Skip validation during creation to avoid premature validation errors
                // The entire object will be validated at the end
                $contact = Contact::fromArray($contactData, true);
                $this->contactObjects[] = $contact;
                $this->data['contacts'][] = $contact->toArray();
            }
        }

        // Handle custom data
        if (isset($data['custom_data']) && is_array($data['custom_data'])) {
            $this->customData = $data['custom_data'];
            $this->data['custom_data'] = $this->customData;
        }

        return $this;
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
     * Set the meta object
     *
     * @param Meta $meta The meta data
     * @return $this
     */
    public function setMeta(Meta $meta): self
    {
        $this->meta = $meta;
        return $this->set('meta', $meta->toArray());
    }

    /**
     * Set the schema version
     *
     * @param string $version The schema version
     * @return $this
     *
     */
    public function setSchemaVersion(string $version): self
    {
        $this->meta->setSchemaVersion($version);
        return $this->set('meta', $this->meta->toArray());
    }

    /**
     * Set the organization name
     *
     * @param string $name The organization name
     * @return $this
     */
    public function setName(string $name): self
    {
        return $this->set('name', $name);
    }

    /**
     * Set the additional name
     *
     * @param string|null $additionalName The additional name or null to remove
     * @return $this
     */
    public function setAdditionalName(?string $additionalName): self
    {
        return $this->set('additional_name', $additionalName);
    }

    /**
     * Create and set identifiers
     *
     * @param string|int $customerNumber The customer number
     * @param string|int|null $externalId The external ID
     * @return $this
     *
     */
    public function createIdentifiers($customerNumber, $externalId = null): self
    {
        $identifiers = new Identifiers();
        $identifiers->setCustomerNumber($customerNumber);

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
     * Set the customer number
     *
     * @param string|int $customerNumber The customer number
     * @return $this
     *
     */
    public function setCustomerNumber($customerNumber): self
    {
        if ($this->identifiers === null) {
            return $this->createIdentifiers($customerNumber);
        }

        $this->identifiers->setCustomerNumber($customerNumber);
        return $this->set('identifiers', $this->identifiers->toArray());
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
            if ($externalId === null) {
                throw new InvalidArgumentException("Cannot set external_id to null when identifiers is not set. You must provide a customer_number.");
            }

            return $this->createIdentifiers($externalId, $externalId);
        }

        $this->identifiers->setExternalId($externalId);
        return $this->set('identifiers', $this->identifiers->toArray());
    }

    /**
     * Create and set a new address from components
     *
     * @param string $postalCode The postal code
     * @param string $locality The city/town
     * @param string $country The country code (ISO 3166-1 alpha-2)
     * @return $this
     *
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
     *
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
     *
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
     *
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
     * Set the route (street)
     *
     * @param string|null $route The route/street or null to remove
     * @return $this
     *
     */
    public function setRoute(?string $route): self
    {
        if (!$this->address) {
            throw new InvalidArgumentException("Cannot set route on organization without an address. Create an address first.");
        }

        $this->address->setRoute($route);
        return $this->set('address', $this->address->toArray());
    }

    /**
     * Set the street (alias for setRoute)
     *
     * @param string|null $street The street or null to remove
     * @return $this
     *
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
     *
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
     * Create and set social media
     *
     * @param string|null $web The website URL
     * @return $this
     *
     */
    public function createSocialMedia(?string $web = null): self
    {
        $socialMedia = new SocialMedia();

        if ($web !== null) {
            $socialMedia->setWeb($web);
        }

        $this->socialMedia = $socialMedia;
        return $this->set('social_media', $socialMedia->toArray());
    }

    /**
     * Set the social media
     *
     * @param SocialMedia $socialMedia The social media
     * @return $this
     */
    public function setSocialMedia(SocialMedia $socialMedia): self
    {
        $this->socialMedia = $socialMedia;
        return $this->set('social_media', $socialMedia->toArray());
    }

    /**
     * Set the website (creates social media if needed)
     *
     * @param string|null $website The website URL or null to remove
     * @return $this
     *
     */
    public function setWebsite(?string $website): self
    {
        if ($this->socialMedia === null) {
            if ($website === null) {
                return $this;
            }

            return $this->createSocialMedia($website);
        }

        $this->socialMedia->setWeb($website);
        return $this->set('social_media', $this->socialMedia->toArray());
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
     *
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
     *
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
     * Set the mobile phone number (alias for setMobilePhone)
     *
     * @param string|null $mobilePhone The mobile phone number or null to remove
     * @return $this
     */
    public function setMobile(?string $mobilePhone): self
    {
        return $this->setMobilePhone($mobilePhone);
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
            if ($fax === null) {
                return $this;
            }

            return $this->createCommunications(null, null, null, $fax);
        }

        $this->communications->setFax($fax);
        return $this->set('communications', $this->communications->toArray());
    }

    /**
     * Create and set business information
     *
     * @param string|null $vatId The VAT ID
     * @param string|null $iban The IBAN
     * @return $this
     *
     */
    public function createBusinessInformation(?string $vatId = null, ?string $iban = null): self
    {
        $businessInfo = new BusinessInformation();

        if ($vatId !== null) {
            $businessInfo->setVatId($vatId);
        }

        if ($iban !== null) {
            $businessInfo->setIban($iban);
        }

        $this->businessInformation = $businessInfo;
        return $this->set('business_information', $businessInfo->toArray());
    }

    /**
     * Set the business information
     *
     * @param BusinessInformation $businessInfo The business information
     * @return $this
     */
    public function setBusinessInformation(BusinessInformation $businessInfo): self
    {
        $this->businessInformation = $businessInfo;
        return $this->set('business_information', $businessInfo->toArray());
    }

    /**
     * Set the VAT ID (creates business information if needed)
     *
     * @param string|null $vatId The VAT ID or null to remove
     * @return $this
     *
     */
    public function setVatId(?string $vatId): self
    {
        if ($this->businessInformation === null) {
            if ($vatId === null) {
                return $this;
            }

            return $this->createBusinessInformation($vatId);
        }

        $this->businessInformation->setVatId($vatId);
        return $this->set('business_information', $this->businessInformation->toArray());
    }

    /**
     * Set the IBAN (creates business information if needed)
     *
     * @param string|null $iban The IBAN or null to remove
     * @return $this
     *
     */
    public function setIban(?string $iban): self
    {
        if ($this->businessInformation === null) {
            if ($iban === null) {
                return $this;
            }

            return $this->createBusinessInformation(null, $iban);
        }

        $this->businessInformation->setIban($iban);
        return $this->set('business_information', $this->businessInformation->toArray());
    }

    /**
     * Create and set agent
     *
     * @param string|int|null $externalId The external ID
     * @return $this
     *
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
     *
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
     * Create and add a contact to the organization
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param array $additionalData Additional contact data
     * @return $this
     */
    public function addContact(string $firstName, string $lastName, array $additionalData = []): self
    {
        $contact = new Contact();
        $contact->setFirstName($firstName)
                ->setLastName($lastName);

        // Set additional data using setters when available
        foreach ($additionalData as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($contact, $method)) {
                $contact->$method($value);
            } else {
                $contact->setCustomField($key, $value);
            }
        }

        // Add the contact to the organization
        if (!isset($this->data['contacts'])) {
            $this->data['contacts'] = [];
            $this->contactObjects = [];
        }

        $this->contactObjects[] = $contact;
        $this->data['contacts'][] = $contact->toArray();

        return $this;
    }

    /**
     * Add a pre-configured contact object to the organization
     *
     * @param Contact $contact The contact to add
     * @return $this
     */
    public function addContactObject(Contact $contact): self
    {
        // Add the contact to the organization
        if (!isset($this->data['contacts'])) {
            $this->data['contacts'] = [];
            $this->contactObjects = [];
        }

        $this->contactObjects[] = $contact;
        $this->data['contacts'][] = $contact->toArray();

        return $this;
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
     * Get the organization name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->get('name');
    }

    /**
     * Get the additional name
     *
     * @return string|null
     */
    public function getAdditionalName(): ?string
    {
        return $this->get('additional_name');
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
     * Get the address
     *
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Get the social media
     *
     * @return SocialMedia|null
     */
    public function getSocialMedia(): ?SocialMedia
    {
        return $this->socialMedia;
    }

    /**
     * Get the website URL
     *
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->socialMedia ? $this->socialMedia->getWeb() : null;
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
     * Get the business information
     *
     * @return BusinessInformation|null
     */
    public function getBusinessInformation(): ?BusinessInformation
    {
        return $this->businessInformation;
    }

    /**
     * Get the VAT ID
     *
     * @return string|null
     */
    public function getVatId(): ?string
    {
        return $this->businessInformation ? $this->businessInformation->getVatId() : null;
    }

    /**
     * Get the IBAN
     *
     * @return string|null
     */
    public function getIban(): ?string
    {
        return $this->businessInformation ? $this->businessInformation->getIban() : null;
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
     * Get the contacts
     *
     * @return array
     */
    public function getContacts(): array
    {
        return $this->contactObjects;
    }

    /**
     * Send the organization to the Ameax API
     *
     * @return array The API response
     * @throws InvalidArgumentException If no API client is set
     */
    public function sendToAmeax(): array
    {
        if (!$this->apiClient) {
            throw new InvalidArgumentException(
                'No API client set. Use setApiClient() before calling sendToAmeax().'
            );
        }

        return $this->apiClient->sendOrganization($this->data);
    }
}

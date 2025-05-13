<?php

namespace Ameax\AmeaxJsonImportApi\Models;

class Communications extends BaseModel
{
    /**
     * Constructor initializes an empty communications object
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Populate the model with data using setters
     *
     * @return $this
     */
    protected function populate(array $data): self
    {
        if (isset($data['phone_number'])) {
            $this->setPhoneNumber($data['phone_number']);
        }

        if (isset($data['mobile_phone'])) {
            $this->setMobilePhone($data['mobile_phone']);
        }

        if (isset($data['email'])) {
            $this->setEmail($data['email']);
        }

        if (isset($data['fax'])) {
            $this->setFax($data['fax']);
        }

        // Handle any other fields
        foreach ($data as $key => $value) {
            if (! in_array($key, ['phone_number', 'mobile_phone', 'email', 'fax'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Set the phone number
     *
     * @param  string|null  $phoneNumber  The phone number or null to remove
     * @return $this
     */
    public function setPhoneNumber(?string $phoneNumber): self
    {
        if ($phoneNumber === null) {
            return $this->set('phone_number', null);
        }

        return $this->set('phone_number', $phoneNumber);
    }

    /**
     * Set the mobile phone number
     *
     * @param  string|null  $mobilePhone  The mobile phone number or null to remove
     * @return $this
     */
    public function setMobilePhone(?string $mobilePhone): self
    {
        if ($mobilePhone === null) {
            return $this->set('mobile_phone', null);
        }

        return $this->set('mobile_phone', $mobilePhone);
    }

    /**
     * Set the email address
     *
     * @param  string|null  $email  The email address or null to remove
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        if ($email === null) {
            return $this->set('email', null);
        }

        // Basic sanitization
        $email = trim($email);

        // Convert to lowercase (email addresses are case-insensitive)
        $email = strtolower($email);

        return $this->set('email', $email);
    }

    /**
     * Set the fax number
     *
     * @param  string|null  $fax  The fax number or null to remove
     * @return $this
     */
    public function setFax(?string $fax): self
    {
        if ($fax === null) {
            return $this->set('fax', null);
        }

        return $this->set('fax', $fax);
    }

    /**
     * Get the phone number
     */
    public function getPhoneNumber(): ?string
    {
        return $this->get('phone_number');
    }

    /**
     * Get the mobile phone number
     */
    public function getMobilePhone(): ?string
    {
        return $this->get('mobile_phone');
    }

    /**
     * Get the email address
     */
    public function getEmail(): ?string
    {
        return $this->get('email');
    }

    /**
     * Get the fax number
     */
    public function getFax(): ?string
    {
        return $this->get('fax');
    }
}

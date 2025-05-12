<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;

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
     * @param array $data
     * @return $this
     * @throws ValidationException If validation fails
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
            if (!in_array($key, ['phone_number', 'mobile_phone', 'email', 'fax'])) {
                $this->set($key, $value);
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
        
        if ($this->has('email') && $this->get('email') !== null) {
            try {
                Validator::email($this->get('email'), 'Email');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if ($this->has('phone_number') && $this->get('phone_number') !== null) {
            try {
                Validator::phoneNumber($this->get('phone_number'), 'Phone number');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if ($this->has('mobile_phone') && $this->get('mobile_phone') !== null) {
            try {
                Validator::phoneNumber($this->get('mobile_phone'), 'Mobile phone');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if ($this->has('fax') && $this->get('fax') !== null) {
            try {
                Validator::phoneNumber($this->get('fax'), 'Fax');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
    }
    
    /**
     * Set the phone number
     *
     * @param string|null $phoneNumber The phone number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setPhoneNumber(?string $phoneNumber): self
    {
        if ($phoneNumber === null) {
            return $this->set('phone_number', null);
        }
        
        Validator::string($phoneNumber, 'Phone number');
        Validator::phoneNumber($phoneNumber, 'Phone number');
        
        return $this->set('phone_number', $phoneNumber);
    }
    
    /**
     * Set the mobile phone number
     *
     * @param string|null $mobilePhone The mobile phone number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setMobilePhone(?string $mobilePhone): self
    {
        if ($mobilePhone === null) {
            return $this->set('mobile_phone', null);
        }
        
        Validator::string($mobilePhone, 'Mobile phone');
        Validator::phoneNumber($mobilePhone, 'Mobile phone');
        
        return $this->set('mobile_phone', $mobilePhone);
    }
    
    /**
     * Set the email address
     *
     * @param string|null $email The email address or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setEmail(?string $email): self
    {
        if ($email === null) {
            return $this->set('email', null);
        }
        
        Validator::string($email, 'Email');
        Validator::email($email, 'Email');
        
        return $this->set('email', $email);
    }
    
    /**
     * Set the fax number
     *
     * @param string|null $fax The fax number or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setFax(?string $fax): self
    {
        if ($fax === null) {
            return $this->set('fax', null);
        }
        
        Validator::string($fax, 'Fax');
        Validator::phoneNumber($fax, 'Fax');
        
        return $this->set('fax', $fax);
    }
    
    /**
     * Get the phone number
     *
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->get('phone_number');
    }
    
    /**
     * Get the mobile phone number
     *
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->get('mobile_phone');
    }
    
    /**
     * Get the email address
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->get('email');
    }
    
    /**
     * Get the fax number
     *
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->get('fax');
    }
}
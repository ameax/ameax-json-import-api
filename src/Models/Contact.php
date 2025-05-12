<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;

class Contact extends BaseModel
{
    /**
     * Create a new contact with required fields
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @return static
     * @throws ValidationException If validation fails
     */
    public static function create(string $firstName, string $lastName): self
    {
        $contact = new static();
        
        $contact->setFirstName($firstName)
                ->setLastName($lastName);
        
        return $contact;
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
        if (isset($data['first_name'])) {
            $this->setFirstName($data['first_name']);
        }
        
        if (isset($data['last_name'])) {
            $this->setLastName($data['last_name']);
        }
        
        if (isset($data['email'])) {
            $this->setEmail($data['email']);
        }
        
        if (isset($data['phone'])) {
            $this->setPhone($data['phone']);
        }
        
        if (isset($data['job_title'])) {
            $this->setJobTitle($data['job_title']);
        }
        
        if (isset($data['department'])) {
            $this->setDepartment($data['department']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['first_name', 'last_name', 'email', 'phone', 'job_title', 'department'])) {
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
        
        // Required fields
        if (!$this->has('first_name')) {
            $errors[] = "Contact first_name is required";
        }
        
        if (!$this->has('last_name')) {
            $errors[] = "Contact last_name is required";
        }
        
        // Optional fields validation
        if ($this->has('email')) {
            try {
                Validator::email($this->get('email'), 'Contact email');
            } catch (ValidationException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }
        
        if ($this->has('phone')) {
            try {
                Validator::phoneNumber($this->get('phone'), 'Contact phone');
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
        Validator::maxLength($firstName, 100, 'First name');
        
        return $this->set('first_name', $firstName);
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
        Validator::maxLength($lastName, 100, 'Last name');
        
        return $this->set('last_name', $lastName);
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
     * Set the job title
     *
     * @param string|null $jobTitle The job title or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setJobTitle(?string $jobTitle): self
    {
        if ($jobTitle === null) {
            return $this->remove('job_title');
        }
        
        Validator::string($jobTitle, 'Job title');
        Validator::maxLength($jobTitle, 100, 'Job title');
        
        return $this->set('job_title', $jobTitle);
    }
    
    /**
     * Set the department
     *
     * @param string|null $department The department or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setDepartment(?string $department): self
    {
        if ($department === null) {
            return $this->remove('department');
        }
        
        Validator::string($department, 'Department');
        Validator::maxLength($department, 100, 'Department');
        
        return $this->set('department', $department);
    }
    
    /**
     * Set a custom field
     *
     * @param string $key The field key
     * @param mixed $value The field value
     * @return $this
     */
    public function setCustomField(string $key, $value): self
    {
        return $this->set($key, $value);
    }
}
<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;

class Employment extends BaseModel
{
    /**
     * Constructor initializes an empty employment object
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
        if (isset($data['job_title'])) {
            $this->setJobTitle($data['job_title']);
        }
        
        if (isset($data['department'])) {
            $this->setDepartment($data['department']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['job_title', 'department'])) {
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
        
        if ($this->has('job_title') && $this->get('job_title') !== null) {
            if (strlen($this->get('job_title')) > 100) {
                $errors[] = "Job title cannot exceed 100 characters";
            }
        }
        
        if ($this->has('department') && $this->get('department') !== null) {
            if (strlen($this->get('department')) > 100) {
                $errors[] = "Department cannot exceed 100 characters";
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
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
            return $this->set('job_title', null);
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
            return $this->set('department', null);
        }
        
        Validator::string($department, 'Department');
        Validator::maxLength($department, 100, 'Department');
        
        return $this->set('department', $department);
    }
    
    /**
     * Get the job title
     *
     * @return string|null
     */
    public function getJobTitle(): ?string
    {
        return $this->get('job_title');
    }
    
    /**
     * Get the department
     *
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->get('department');
    }
}
<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;

class SocialMedia extends BaseModel
{
    /**
     * Constructor initializes an empty social media object
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
        if (isset($data['web'])) {
            $this->setWeb($data['web']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['web'])) {
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
        
        if ($this->has('web') && $this->get('web') !== null) {
            try {
                Validator::url($this->get('web'), 'Web URL');
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
     * Set the web URL
     *
     * @param string|null $url The website URL or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setWeb(?string $url): self
    {
        if ($url === null) {
            return $this->set('web', null);
        }
        
        Validator::string($url, 'Web URL');
        Validator::url($url, 'Web URL');
        
        return $this->set('web', $url);
    }
    
    /**
     * Get the web URL
     *
     * @return string|null
     */
    public function getWeb(): ?string
    {
        return $this->get('web');
    }
}
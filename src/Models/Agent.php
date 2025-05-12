<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

class Agent extends BaseModel
{
    /**
     * Constructor initializes an empty agent object
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
        if (isset($data['external_id'])) {
            $this->setExternalId($data['external_id']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['external_id'])) {
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
        // No specific validation rules for agent at this time
        return true;
    }
    
    /**
     * Set the external ID
     *
     * @param string|int|null $externalId The external ID or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setExternalId($externalId): self
    {
        if ($externalId === null) {
            return $this->set('external_id', null);
        }
        
        if (!is_string($externalId) && !is_int($externalId)) {
            throw new ValidationException(["External ID must be a string, integer, or null"]);
        }
        
        return $this->set('external_id', $externalId);
    }
    
    /**
     * Get the external ID
     *
     * @return string|int|null
     */
    public function getExternalId()
    {
        return $this->get('external_id');
    }
}
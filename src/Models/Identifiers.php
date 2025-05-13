<?php

namespace Ameax\AmeaxJsonImportApi\Models;




class Identifiers extends BaseModel
{
    /**
     * Constructor initializes an empty identifiers object
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
     * 
     */
    protected function populate(array $data): self
    {
        if (isset($data['customer_number'])) {
            $this->setCustomerNumber($data['customer_number']);
        }
        
        if (isset($data['external_id'])) {
            $this->setExternalId($data['external_id']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['customer_number', 'external_id'])) {
                $this->set($key, $value);
            }
        }
        
        return $this;
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
        if (is_int($customerNumber)) {
            $customerNumber = (string) $customerNumber;
        }
        
        if (!is_string($customerNumber) && !is_int($customerNumber)) {
            throw new InvalidArgumentException("Customer number must be a string or integer");
        }
        
        return $this->set('customer_number', $customerNumber);
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
        if ($externalId === null) {
            return $this->remove('external_id');
        }
        
        if (is_int($externalId)) {
            $externalId = (string) $externalId;
        }
        
        if (!is_string($externalId) && !is_int($externalId)) {
            throw new InvalidArgumentException("External ID must be a string, integer, or null");
        }
        
        return $this->set('external_id', $externalId);
    }
    
    /**
     * Get the customer number
     *
     * @return string|int|null
     */
    public function getCustomerNumber()
    {
        return $this->get('customer_number');
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
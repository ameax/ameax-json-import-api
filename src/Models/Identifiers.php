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
     * @param string|int|mixed $customerNumber The customer number
     * @return $this
     */
    public function setCustomerNumber($customerNumber): self
    {
        // Convert to string regardless of input type
        $customerNumber = (string) $customerNumber;
        
        return $this->set('customer_number', $customerNumber);
    }
    
    /**
     * Set the external ID
     *
     * @param string|int|null|mixed $externalId The external ID or null to remove
     * @return $this
     */
    public function setExternalId($externalId): self
    {
        if ($externalId === null) {
            return $this->remove('external_id');
        }
        
        // Convert to string regardless of input type
        $externalId = (string) $externalId;
        
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
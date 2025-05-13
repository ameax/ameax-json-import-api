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
     * @return $this
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
            if (! in_array($key, ['customer_number', 'external_id'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Set the customer number
     * Must be a numerical string or null if empty
     *
     * @param  string|int|null  $customerNumber  The customer number
     * @return $this
     */
    public function setCustomerNumber(string|int|null $customerNumber): self
    {
        // Handle null or empty string case
        if ($customerNumber === null || $customerNumber === '') {
            return $this->set('customer_number', null);
        }
        
        // Convert to string
        $customerNumber = (string) $customerNumber;
        
        // Validate that it contains only numeric characters
        if (!ctype_digit($customerNumber)) {
            throw new \InvalidArgumentException('Customer number must contain only numeric characters.');
        }

        return $this->set('customer_number', $customerNumber);
    }

    /**
     * Set the external ID
     *
     * @param  string|int|null  $externalId  The external ID or null to remove
     * @return $this
     */
    public function setExternalId(string|int|null $externalId): self
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
     */
    public function getCustomerNumber(): ?string
    {
        return $this->get('customer_number');
    }

    /**
     * Get the external ID
     */
    public function getExternalId(): ?string
    {
        return $this->get('external_id');
    }
}

<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;

class BusinessInformation extends BaseModel
{
    /**
     * Constructor initializes an empty business information object
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
        if (isset($data['vat_id'])) {
            $this->setVatId($data['vat_id']);
        }
        
        if (isset($data['iban'])) {
            $this->setIban($data['iban']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['vat_id', 'iban'])) {
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
        
        if ($this->has('vat_id') && $this->get('vat_id') !== null) {
            // Basic validation for VAT ID
            if (strlen($this->get('vat_id')) > 50) {
                $errors[] = "VAT ID cannot exceed 50 characters";
            }
        }
        
        if ($this->has('iban') && $this->get('iban') !== null) {
            // Basic validation for IBAN
            if (strlen($this->get('iban')) > 34) {
                $errors[] = "IBAN cannot exceed 34 characters";
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
    }
    
    /**
     * Set the VAT ID
     *
     * @param string|null $vatId The VAT ID or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setVatId(?string $vatId): self
    {
        if ($vatId === null) {
            return $this->set('vat_id', null);
        }
        
        Validator::string($vatId, 'VAT ID');
        Validator::maxLength($vatId, 50, 'VAT ID');
        
        return $this->set('vat_id', $vatId);
    }
    
    /**
     * Set the IBAN
     *
     * @param string|null $iban The IBAN or null to remove
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setIban(?string $iban): self
    {
        if ($iban === null) {
            return $this->set('iban', null);
        }
        
        Validator::string($iban, 'IBAN');
        Validator::maxLength($iban, 34, 'IBAN');
        
        return $this->set('iban', $iban);
    }
    
    /**
     * Get the VAT ID
     *
     * @return string|null
     */
    public function getVatId(): ?string
    {
        return $this->get('vat_id');
    }
    
    /**
     * Get the IBAN
     *
     * @return string|null
     */
    public function getIban(): ?string
    {
        return $this->get('iban');
    }
}
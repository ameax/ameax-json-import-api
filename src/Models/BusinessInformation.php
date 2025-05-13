<?php

namespace Ameax\AmeaxJsonImportApi\Models;

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
     * @return $this
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
            if (! in_array($key, ['vat_id', 'iban'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Set the VAT ID
     *
     * @param  string|null  $vatId  The VAT ID or null to remove
     * @return $this
     */
    public function setVatId(?string $vatId): self
    {
        if ($vatId === null) {
            return $this->set('vat_id', null);
        }

        return $this->set('vat_id', $vatId);
    }

    /**
     * Set the IBAN
     *
     * @param  string|null  $iban  The IBAN or null to remove
     * @return $this
     */
    public function setIban(?string $iban): self
    {
        if ($iban === null) {
            return $this->set('iban', null);
        }

        return $this->set('iban', $iban);
    }

    /**
     * Get the VAT ID
     */
    public function getVatId(): ?string
    {
        return $this->get('vat_id');
    }

    /**
     * Get the IBAN
     */
    public function getIban(): ?string
    {
        return $this->get('iban');
    }
}

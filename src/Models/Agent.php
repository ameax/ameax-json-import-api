<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use InvalidArgumentException;

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
     *
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
     * Set the external ID
     *
     * @param string|int|null $externalId The external ID or null to remove
     * @return $this
     *
     */
    public function setExternalId($externalId): self
    {
        if ($externalId === null) {
            return $this->set('external_id', null);
        }

        if (!is_string($externalId) && !is_int($externalId)) {
            throw new InvalidArgumentException("External ID must be a string, integer, or null");
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

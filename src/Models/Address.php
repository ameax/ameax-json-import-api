<?php

namespace Ameax\AmeaxJsonImportApi\Models;

class Address extends BaseModel
{
    /**
     * Constructor initializes an empty address
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
     */
    protected function populate(array $data): self
    {
        if (isset($data['postal_code'])) {
            $this->setPostalCode($data['postal_code']);
        }

        if (isset($data['locality'])) {
            $this->setLocality($data['locality']);
        }

        if (isset($data['country'])) {
            $this->setCountry($data['country']);
        }

        if (isset($data['route'])) {
            $this->setRoute($data['route']);
        } elseif (isset($data['street'])) {
            // Backward compatibility
            $this->setStreet($data['street']);
        }

        if (isset($data['house_number'])) {
            $this->setHouseNumber($data['house_number']);
        }

        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['postal_code', 'locality', 'country', 'route', 'street', 'house_number'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }


    /**
     * Set the postal code
     *
     * @param string $postalCode The postal code
     * @return $this
     */
    public function setPostalCode(string $postalCode): self
    {
        return $this->set('postal_code', $postalCode);
    }

    /**
     * Set the locality (city/town)
     *
     * @param string $locality The locality
     * @return $this
     */
    public function setLocality(string $locality): self
    {
        return $this->set('locality', $locality);
    }

    /**
     * Set the country code
     *
     * @param string $country The country code (ISO 3166-1 alpha-2)
     * @return $this
     */
    public function setCountry(string $country): self
    {
        return $this->set('country', strtoupper($country));
    }

    /**
     * Set the route (street)
     *
     * @param string|null $route The route/street or null to remove
     * @return $this
     */
    public function setRoute(?string $route): self
    {
        return $this->set('route', $route);
    }

    /**
     * Set the street (alias for setRoute)
     *
     * @param string|null $street The street or null to remove
     * @return $this
     */
    public function setStreet(?string $street): self
    {
        return $this->setRoute($street);
    }

    /**
     * Set the house number
     *
     * @param string|null $houseNumber The house number or null to remove
     * @return $this
     */
    public function setHouseNumber(?string $houseNumber): self
    {
        if ($houseNumber === null) {
            return $this->remove('house_number');
        }

        return $this->set('house_number', $houseNumber);
    }

    /**
     * Set a custom field
     *
     * @param string $key The field key
     * @param mixed $value The field value
     * @return $this
     */
    public function setCustomField(string $key, mixed $value): self
    {
        return $this->set($key, $value);
    }
}

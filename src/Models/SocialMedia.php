<?php

namespace Ameax\AmeaxJsonImportApi\Models;

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
     * @return $this
     */
    protected function populate(array $data): self
    {
        if (isset($data['web'])) {
            $this->setWeb($data['web']);
        }

        // Handle any other fields
        foreach ($data as $key => $value) {
            if (! in_array($key, ['web'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Set the web URL
     *
     * @param  string|null  $url  The website URL or null to remove
     * @return $this
     */
    public function setWeb(?string $url): self
    {
        if ($url === null) {
            return $this->set('web', null);
        }

        return $this->set('web', $url);
    }

    /**
     * Get the web URL
     */
    public function getWeb(): ?string
    {
        return $this->get('web');
    }
}

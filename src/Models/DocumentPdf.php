<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use InvalidArgumentException;

class DocumentPdf extends BaseModel
{
    public const TYPE_BASE64 = 'base64';

    public const TYPE_URL = 'url';

    /**
     * Populate the model with data using setters
     *
     * @param  array<string, mixed>  $data
     * @return $this
     */
    protected function populate(array $data): self
    {
        if (isset($data['type'])) {
            $this->setType($data['type']);
        }

        if (isset($data['content'])) {
            $this->setContent($data['content']);
        }

        if (isset($data['url'])) {
            $this->setUrl($data['url']);
        }

        return $this;
    }

    /**
     * Set the PDF document type
     *
     * @param  string  $type  The type ('base64' or 'url')
     * @return $this
     */
    public function setType(string $type): self
    {
        $validTypes = [self::TYPE_BASE64, self::TYPE_URL];

        if (! in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Invalid PDF type. Valid types are: '.implode(', ', $validTypes));
        }

        return $this->set('type', $type);
    }

    /**
     * Set the base64 encoded PDF content (only valid when type is 'base64')
     *
     * @param  string  $content  The base64 encoded PDF content
     * @return $this
     */
    public function setContent(string $content): self
    {
        return $this->set('content', $content);
    }

    /**
     * Set the URL to the PDF document (only valid when type is 'url')
     *
     * @param  string  $url  The HTTPS URL to the PDF document
     * @return $this
     */
    public function setUrl(string $url): self
    {
        if (! filter_var($url, FILTER_VALIDATE_URL) || ! str_starts_with($url, 'https://')) {
            throw new InvalidArgumentException('URL must be a valid HTTPS URL');
        }

        return $this->set('url', $url);
    }

    /**
     * Get the PDF document type
     */
    public function getType(): ?string
    {
        return $this->get('type');
    }

    /**
     * Get the base64 encoded PDF content
     */
    public function getContent(): ?string
    {
        return $this->get('content');
    }

    /**
     * Get the PDF document URL
     */
    public function getUrl(): ?string
    {
        return $this->get('url');
    }

    /**
     * Create a new DocumentPdf instance with base64 content
     *
     * @param  string  $base64Content  The base64 encoded PDF content
     */
    public static function fromBase64(string $base64Content): self
    {
        $instance = new self;
        $instance->setType(self::TYPE_BASE64);
        $instance->setContent($base64Content);

        return $instance;
    }

    /**
     * Create a new DocumentPdf instance with URL
     *
     * @param  string  $url  The HTTPS URL to the PDF document
     */
    public static function fromUrl(string $url): self
    {
        $instance = new self;
        $instance->setType(self::TYPE_URL);
        $instance->setUrl($url);

        return $instance;
    }
}

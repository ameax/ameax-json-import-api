<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use JsonSerializable;

abstract class BaseModel implements JsonSerializable
{
    protected array $data = [];
    
    /**
     * Create a new model instance from an array of data
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $instance = new static();
        $instance->data = $data;
        
        return $instance;
    }
    
    /**
     * Convert the model instance to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
    
    /**
     * Set a value in the data array
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    protected function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        
        return $this;
    }
    
    /**
     * Get a value from the data array
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }
    
    /**
     * Check if a key exists in the data array
     *
     * @param string $key
     * @return bool
     */
    protected function has(string $key): bool
    {
        return isset($this->data[$key]);
    }
    
    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
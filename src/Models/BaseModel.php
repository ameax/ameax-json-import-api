<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use JsonSerializable;

abstract class BaseModel implements JsonSerializable
{
    protected array $data = [];

    /**
     * Create a new model instance from an array of data
     *
     * @param  array  $data  The data to populate the model with
     */
    public static function fromArray(array $data): static
    {
        /** @var static $instance */
        $instance = new static();
        $instance->populate($data);

        return $instance;
    }

    /**
     * Populate the model with data using setters
     *
     * @return $this
     */
    abstract protected function populate(array $data): self;

    /**
     * Convert the model instance to an array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Set a value in the data array with dot notation support
     *
     * @param  string  $key  The key to set (can use dot notation)
     * @param  mixed  $value  The value to set
     * @return $this
     */
    protected function set(string $key, mixed $value): self
    {
        if (strpos($key, '.') === false) {
            $this->data[$key] = $value;

            return $this;
        }

        $keys = explode('.', $key);
        $lastKey = array_pop($keys);

        $current = &$this->data;
        foreach ($keys as $nestedKey) {
            if (! isset($current[$nestedKey]) || ! is_array($current[$nestedKey])) {
                $current[$nestedKey] = [];
            }

            $current = &$current[$nestedKey];
        }

        $current[$lastKey] = $value;

        return $this;
    }

    /**
     * Get a value from the data array with dot notation support
     *
     * @param  string  $key  The key to get (can use dot notation)
     * @param  mixed  $default  The default value if key doesn't exist
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        if (strpos($key, '.') === false) {
            return $this->data[$key] ?? $default;
        }

        $keys = explode('.', $key);
        $value = $this->data;

        foreach ($keys as $nestedKey) {
            if (! isset($value[$nestedKey])) {
                return $default;
            }

            $value = $value[$nestedKey];
        }

        return $value;
    }

    /**
     * Check if a key exists in the data array with dot notation support
     *
     * @param  string  $key  The key to check (can use dot notation)
     */
    protected function has(string $key): bool
    {
        if (strpos($key, '.') === false) {
            return isset($this->data[$key]);
        }

        $keys = explode('.', $key);
        $value = $this->data;

        foreach ($keys as $nestedKey) {
            if (! isset($value[$nestedKey])) {
                return false;
            }

            $value = $value[$nestedKey];
        }

        return true;
    }

    /**
     * Remove a key from the data array with dot notation support
     *
     * @param  string  $key  The key to remove (can use dot notation)
     * @return $this
     */
    protected function remove(string $key): self
    {
        if (strpos($key, '.') === false) {
            unset($this->data[$key]);

            return $this;
        }

        $keys = explode('.', $key);
        $lastKey = array_pop($keys);

        $current = &$this->data;
        foreach ($keys as $nestedKey) {
            if (! isset($current[$nestedKey])) {
                return $this;
            }

            $current = &$current[$nestedKey];
        }

        unset($current[$lastKey]);

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

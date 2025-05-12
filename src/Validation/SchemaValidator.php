<?php

namespace Ameax\AmeaxJsonImportApi\Validation;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use JsonSchema\Validator;

class SchemaValidator
{
    /**
     * Validate data against a JSON schema
     *
     * @param array $data The data to validate
     * @param string $schemaPath The path to the schema file
     * @return bool True if validation passes
     * @throws ValidationException If validation fails
     */
    public static function validate(array $data, string $schemaPath): bool
    {
        if (!file_exists($schemaPath)) {
            throw new \InvalidArgumentException("Schema file not found at: {$schemaPath}");
        }
        
        $schema = json_decode(file_get_contents($schemaPath));
        $validator = new Validator();
        $validator->validate(json_decode(json_encode($data)), $schema);
        
        if (!$validator->isValid()) {
            $errors = [];
            foreach ($validator->getErrors() as $error) {
                $errors[] = sprintf("[%s] %s", $error['property'], $error['message']);
            }
            throw new ValidationException($errors);
        }
        
        return true;
    }
    
    /**
     * Validate data against a JSON schema string
     *
     * @param array $data The data to validate
     * @param string $schemaJson The JSON schema as a string
     * @return bool True if validation passes
     * @throws ValidationException If validation fails
     */
    public static function validateWithString(array $data, string $schemaJson): bool
    {
        $schema = json_decode($schemaJson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON schema: " . json_last_error_msg());
        }
        
        $validator = new Validator();
        $validator->validate(json_decode(json_encode($data)), $schema);
        
        if (!$validator->isValid()) {
            $errors = [];
            foreach ($validator->getErrors() as $error) {
                $errors[] = sprintf("[%s] %s", $error['property'], $error['message']);
            }
            throw new ValidationException($errors);
        }
        
        return true;
    }
}
<?php

namespace Ameax\AmeaxJsonImportApi\Tests;

use Ameax\AmeaxJsonImportApi\Validation\SchemaValidator;
use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class SchemaValidatorTest extends TestCase
{
    private string $validSchemaJson = '{
        "$schema": "https://json-schema.org/draft/2020-12/schema",
        "title": "Test Schema",
        "type": "object",
        "required": ["name"],
        "properties": {
            "name": {
                "type": "string",
                "minLength": 3
            },
            "email": {
                "type": "string",
                "format": "email"
            }
        }
    }';
    
    private array $validData = [
        'name' => 'Test Company',
        'email' => 'test@example.com'
    ];
    
    private array $invalidData = [
        'name' => 'AB', // Too short
        'email' => 'not-an-email'
    ];
    
    public function testValidateWithValidData(): void
    {
        // Create a temporary schema file
        $tempFile = tempnam(sys_get_temp_dir(), 'schema_');
        file_put_contents($tempFile, $this->validSchemaJson);
        
        try {
            $result = SchemaValidator::validate($this->validData, $tempFile);
            $this->assertTrue($result);
        } finally {
            // Clean up
            unlink($tempFile);
        }
    }
    
    public function testValidateWithInvalidData(): void
    {
        // Create a temporary schema file
        $tempFile = tempnam(sys_get_temp_dir(), 'schema_');
        file_put_contents($tempFile, $this->validSchemaJson);
        
        try {
            $this->expectException(ValidationException::class);
            SchemaValidator::validate($this->invalidData, $tempFile);
        } finally {
            // Clean up
            unlink($tempFile);
        }
    }
    
    public function testValidateWithStringWithValidData(): void
    {
        $result = SchemaValidator::validateWithString($this->validData, $this->validSchemaJson);
        $this->assertTrue($result);
    }
    
    public function testValidateWithStringWithInvalidData(): void
    {
        $this->expectException(ValidationException::class);
        SchemaValidator::validateWithString($this->invalidData, $this->validSchemaJson);
    }
    
    public function testValidateWithNonExistentSchemaFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SchemaValidator::validate($this->validData, '/path/to/nonexistent/schema.json');
    }
    
    public function testValidateWithStringWithInvalidSchemaJson(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SchemaValidator::validateWithString($this->validData, '{invalid-json');
    }
}
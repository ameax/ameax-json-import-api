<?php

namespace Ameax\AmeaxJsonImportApi\Validation;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;

class Validator
{
    /**
     * Validate that a value is not empty
     *
     * @param mixed $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is empty
     */
    public static function notEmpty($value, string $field): void
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            throw new ValidationException(["{$field} cannot be empty"]);
        }
    }
    
    /**
     * Validate that a string has a maximum length
     *
     * @param string $value The string to validate
     * @param int $maxLength The maximum allowed length
     * @param string $field The field name for error messages
     * @throws ValidationException If the string exceeds the maximum length
     */
    public static function maxLength(string $value, int $maxLength, string $field): void
    {
        if (mb_strlen($value) > $maxLength) {
            throw new ValidationException(["{$field} cannot exceed {$maxLength} characters"]);
        }
    }
    
    /**
     * Validate that a string has a minimum length
     *
     * @param string $value The string to validate
     * @param int $minLength The minimum allowed length
     * @param string $field The field name for error messages
     * @throws ValidationException If the string is shorter than the minimum length
     */
    public static function minLength(string $value, int $minLength, string $field): void
    {
        if (mb_strlen($value) < $minLength) {
            throw new ValidationException(["{$field} must be at least {$minLength} characters"]);
        }
    }
    
    /**
     * Validate that a value is a string
     *
     * @param mixed $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not a string
     */
    public static function string($value, string $field): void
    {
        if (!is_string($value)) {
            throw new ValidationException(["{$field} must be a string"]);
        }
    }
    
    /**
     * Validate that a value is numeric
     *
     * @param mixed $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not numeric
     */
    public static function numeric($value, string $field): void
    {
        if (!is_numeric($value)) {
            throw new ValidationException(["{$field} must be numeric"]);
        }
    }
    
    /**
     * Validate that a value is an integer
     *
     * @param mixed $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not an integer
     */
    public static function integer($value, string $field): void
    {
        if (!is_int($value) && (!is_string($value) || !ctype_digit($value))) {
            throw new ValidationException(["{$field} must be an integer"]);
        }
    }
    
    /**
     * Validate that a value matches a regex pattern
     *
     * @param string $value The value to validate
     * @param string $pattern The regex pattern
     * @param string $field The field name for error messages
     * @param string $message Custom error message (optional)
     * @throws ValidationException If the value does not match the pattern
     */
    public static function pattern(string $value, string $pattern, string $field, string $message = null): void
    {
        if (!preg_match($pattern, $value)) {
            $errorMessage = $message ?? "{$field} has an invalid format";
            throw new ValidationException([$errorMessage]);
        }
    }
    
    /**
     * Validate that a value is a valid email address
     *
     * @param string $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not a valid email address
     */
    public static function email(string $value, string $field): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException(["{$field} must be a valid email address"]);
        }
    }
    
    /**
     * Validate that a value is a valid URL
     *
     * @param string $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not a valid URL
     */
    public static function url(string $value, string $field): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new ValidationException(["{$field} must be a valid URL"]);
        }
    }
    
    /**
     * Validate that a value is in a list of allowed values
     *
     * @param mixed $value The value to validate
     * @param array $allowedValues The allowed values
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not in the list of allowed values
     */
    public static function in($value, array $allowedValues, string $field): void
    {
        if (!in_array($value, $allowedValues, true)) {
            $allowedList = implode(', ', $allowedValues);
            throw new ValidationException(["{$field} must be one of: {$allowedList}"]);
        }
    }
    
    /**
     * Validate that a value is a valid ISO 3166-1 alpha-2 country code
     *
     * @param string $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not a valid country code
     */
    public static function countryCode(string $value, string $field): void
    {
        // ISO 3166-1 alpha-2 country codes are two-letter codes
        self::pattern($value, '/^[A-Z]{2}$/', $field, "{$field} must be a valid ISO 3166-1 alpha-2 country code");
    }
    
    /**
     * Validate that a value is a valid postal code format
     * This is a simple validation, as postal code formats vary by country
     *
     * @param string $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not a valid postal code format
     */
    public static function postalCode(string $value, string $field): void
    {
        // Basic validation for postal codes
        self::pattern($value, '/^[A-Za-z0-9\-\s]{2,10}$/', $field, "{$field} must be a valid postal code format");
    }
    
    /**
     * Validate that a value is a valid phone number format
     * This is a simple validation, as phone number formats vary by country
     *
     * @param string $value The value to validate
     * @param string $field The field name for error messages
     * @throws ValidationException If the value is not a valid phone number format
     */
    public static function phoneNumber(string $value, string $field): void
    {
        // Allow +, spaces, parentheses, hyphens, and digits
        self::pattern(
            $value, 
            '/^[\+\s\(\)\-0-9]{6,20}$/', 
            $field, 
            "{$field} must be a valid phone number format"
        );
    }
}
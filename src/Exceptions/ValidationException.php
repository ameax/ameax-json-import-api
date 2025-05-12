<?php

namespace Ameax\AmeaxJsonImportApi\Exceptions;

class ValidationException extends \Exception
{
    protected array $errors;
    
    public function __construct(array $errors, string $message = 'JSON validation failed', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}
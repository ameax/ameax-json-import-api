<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\Exceptions\ValidationException;
use Ameax\AmeaxJsonImportApi\Validation\Validator;

class Meta extends BaseModel
{
    public const DOCUMENT_TYPE_ORGANIZATION = 'ameax_organization_account';
    public const DOCUMENT_TYPE_PRIVATE_PERSON = 'ameax_private_person_account';
    public const SCHEMA_VERSION = '1.0';
    
    /**
     * Constructor initializes a new meta with document type and schema version
     */
    public function __construct(string $documentType = self::DOCUMENT_TYPE_ORGANIZATION)
    {
        $this->data = [
            'document_type' => $documentType,
            'schema_version' => self::SCHEMA_VERSION,
        ];
    }
    
    /**
     * Populate the model with data using setters
     *
     * @param array $data
     * @return $this
     * @throws ValidationException If validation fails
     */
    protected function populate(array $data): self
    {
        // Always ensure document type is valid, but keep the input value if it's valid
        $validDocumentTypes = [self::DOCUMENT_TYPE_ORGANIZATION, self::DOCUMENT_TYPE_PRIVATE_PERSON];
        if (isset($data['document_type']) && in_array($data['document_type'], $validDocumentTypes)) {
            $this->data['document_type'] = $data['document_type'];
        }
        
        if (isset($data['schema_version'])) {
            $this->setSchemaVersion($data['schema_version']);
        } else {
            $this->data['schema_version'] = self::SCHEMA_VERSION;
        }
        
        // Handle import_status if present
        if (isset($data['import_status']) && is_array($data['import_status'])) {
            $this->setImportStatus($data['import_status']);
        }
        
        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['document_type', 'schema_version', 'import_status'])) {
                $this->set($key, $value);
            }
        }
        
        return $this;
    }
    
    /**
     * Validate the model data before saving/sending
     *
     * @return bool True if validation passes
     * @throws ValidationException If validation fails
     */
    public function validate(): bool
    {
        $errors = [];
        
        // Ensure document_type is valid and schema_version is set correctly
        $validDocumentTypes = [self::DOCUMENT_TYPE_ORGANIZATION, self::DOCUMENT_TYPE_PRIVATE_PERSON];
        if (!in_array($this->get('document_type'), $validDocumentTypes)) {
            $errors[] = "Meta document_type must be one of: " . implode(', ', $validDocumentTypes);
        }
        
        if (!$this->has('schema_version')) {
            $errors[] = "Meta schema_version is required";
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
    }
    
    /**
     * Set the schema version
     *
     * @param string $version The schema version
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setSchemaVersion(string $version): self
    {
        Validator::string($version, 'Schema version');
        Validator::notEmpty($version, 'Schema version');
        
        return $this->set('schema_version', $version);
    }
    
    /**
     * Set the import status
     *
     * @param array $status The import status data
     * @return $this
     */
    public function setImportStatus(array $status): self
    {
        return $this->set('import_status', $status);
    }
    
    /**
     * Get the document type
     *
     * @return string
     */
    public function getDocumentType(): string
    {
        return $this->get('document_type');
    }
    
    /**
     * Get the schema version
     *
     * @return string
     */
    public function getSchemaVersion(): string
    {
        return $this->get('schema_version');
    }
    
    /**
     * Get the import status
     *
     * @return array|null
     */
    public function getImportStatus(): ?array
    {
        return $this->get('import_status');
    }
    
    /**
     * Set the document type
     *
     * @param string $type The document type
     * @return $this
     * @throws ValidationException If validation fails
     */
    public function setDocumentType(string $type): self
    {
        Validator::string($type, 'Document type');
        Validator::notEmpty($type, 'Document type');
        
        $validDocumentTypes = [self::DOCUMENT_TYPE_ORGANIZATION, self::DOCUMENT_TYPE_PRIVATE_PERSON];
        if (!in_array($type, $validDocumentTypes)) {
            throw new ValidationException(["Document type must be one of: " . implode(', ', $validDocumentTypes)]);
        }
        
        return $this->set('document_type', $type);
    }
}
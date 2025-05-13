<?php

namespace Ameax\AmeaxJsonImportApi\Models;




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
     * 
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
     * Set the schema version
     *
     * @param string $version The schema version
     * @return $this
     * 
     */
    public function setSchemaVersion(string $version): self
    {
        
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
     * 
     */
    public function setDocumentType(string $type): self
    {
        
        $validDocumentTypes = [self::DOCUMENT_TYPE_ORGANIZATION, self::DOCUMENT_TYPE_PRIVATE_PERSON];
        if (!in_array($type, $validDocumentTypes)) {
            throw new InvalidArgumentException("Document type must be one of: " . implode(', ', $validDocumentTypes));
        }
        
        return $this->set('document_type', $type);
    }
}
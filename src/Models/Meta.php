<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use InvalidArgumentException;

class Meta extends BaseModel
{
    public const DOCUMENT_TYPE_ORGANIZATION = 'ameax_organization_account';

    public const DOCUMENT_TYPE_PRIVATE_PERSON = 'ameax_private_person_account';

    public const DOCUMENT_TYPE_RECEIPT = 'ameax_receipt';

    public const DOCUMENT_TYPE_SALE = 'ameax_sale';

    public const SCHEMA_VERSION = '1.0';

    public const IMPORT_MODE_CREATE_OR_UPDATE = 'create_or_update';

    public const IMPORT_MODE_CREATE_ONLY = 'create_only';

    public const IMPORT_MODE_UPDATE_ONLY = 'update_only';

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
     * @return $this
     */
    protected function populate(array $data): self
    {
        // Always ensure document type is valid, but keep the input value if it's valid
        $validDocumentTypes = [self::DOCUMENT_TYPE_ORGANIZATION, self::DOCUMENT_TYPE_PRIVATE_PERSON, self::DOCUMENT_TYPE_RECEIPT, self::DOCUMENT_TYPE_SALE];
        if (isset($data['document_type']) && in_array($data['document_type'], $validDocumentTypes)) {
            $this->data['document_type'] = $data['document_type'];
        }

        if (isset($data['schema_version'])) {
            $this->setSchemaVersion($data['schema_version']);
        } else {
            $this->data['schema_version'] = self::SCHEMA_VERSION;
        }

        // Handle import_mode if present
        if (isset($data['import_mode'])) {
            $this->setImportMode($data['import_mode']);
        }

        // Handle import_status if present
        if (isset($data['import_status']) && is_array($data['import_status'])) {
            $this->setImportStatus($data['import_status']);
        }

        // Handle any other fields
        foreach ($data as $key => $value) {
            if (! in_array($key, ['document_type', 'schema_version', 'import_mode', 'import_status'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Set the schema version
     *
     * @param  string  $version  The schema version
     * @return $this
     */
    public function setSchemaVersion(string $version): self
    {

        return $this->set('schema_version', $version);
    }

    /**
     * Set the import mode
     *
     * @param  string|null  $mode  The import mode
     * @return $this
     */
    public function setImportMode(?string $mode): self
    {
        if ($mode !== null) {
            $validModes = [self::IMPORT_MODE_CREATE_OR_UPDATE, self::IMPORT_MODE_CREATE_ONLY, self::IMPORT_MODE_UPDATE_ONLY];
            if (! in_array($mode, $validModes)) {
                throw new InvalidArgumentException('Import mode must be one of: '.implode(', ', $validModes).', got: '.$mode);
            }
        }

        return $this->set('import_mode', $mode);
    }

    /**
     * Set the import status
     *
     * @param  array  $status  The import status data
     * @return $this
     */
    public function setImportStatus(array $status): self
    {
        return $this->set('import_status', $status);
    }

    /**
     * Get the document type
     */
    public function getDocumentType(): string
    {
        return $this->get('document_type');
    }

    /**
     * Get the schema version
     */
    public function getSchemaVersion(): string
    {
        return $this->get('schema_version');
    }

    /**
     * Get the import mode
     */
    public function getImportMode(): ?string
    {
        return $this->get('import_mode');
    }

    /**
     * Get the import status
     */
    public function getImportStatus(): ?array
    {
        return $this->get('import_status');
    }

    /**
     * Set the document type
     *
     * @param  string  $type  The document type
     * @return $this
     */
    public function setDocumentType(string $type): self
    {

        $validDocumentTypes = [self::DOCUMENT_TYPE_ORGANIZATION, self::DOCUMENT_TYPE_PRIVATE_PERSON, self::DOCUMENT_TYPE_RECEIPT, self::DOCUMENT_TYPE_SALE];
        if (! in_array($type, $validDocumentTypes)) {
            throw new InvalidArgumentException('Document type must be one of: '.implode(', ', $validDocumentTypes).', got: '.$type);
        }

        return $this->set('document_type', $type);
    }
}

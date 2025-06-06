<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use InvalidArgumentException;

class Receipt extends BaseModel
{
    public const DOCUMENT_TYPE = 'ameax_receipt';

    public const SCHEMA_VERSION = '1.0';

    public const TYPE_OFFER = 'offer';
    public const TYPE_ORDER = 'order';
    public const TYPE_INVOICE = 'invoice';
    public const TYPE_CREDIT_NOTE = 'credit_note';
    public const TYPE_CANCELLATION = 'cancellation_document';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const TAX_MODE_NET = 'net';
    public const TAX_MODE_GROSS = 'gross';

    public const TAX_TYPE_REGULAR = 'regular';
    public const TAX_TYPE_REDUCED = 'reduced';
    public const TAX_TYPE_EXEMPT_EU = 'exempt_eu';
    public const TAX_TYPE_EXEMPT_THIRD = 'exempt_third';
    public const TAX_TYPE_EXEMPT_OTHER = 'exempt_other';

    /**
     * @var AmeaxJsonImportApi|null The API client to use for sending
     */
    protected ?AmeaxJsonImportApi $apiClient = null;

    /**
     * @var Meta The meta data
     */
    protected Meta $meta;

    /**
     * @var Identifiers The identifiers
     */
    protected Identifiers $identifiers;

    /**
     * @var array<LineItem> The line items
     */
    protected array $lineItems = [];

    /**
     * @var array Custom data fields
     */
    protected array $customData = [];

    /**
     * Constructor initializes a new receipt with meta information
     */
    public function __construct()
    {
        $this->meta = new Meta;
        $this->meta->setDocumentType(self::DOCUMENT_TYPE);
        $this->meta->setSchemaVersion(self::SCHEMA_VERSION);

        $this->identifiers = new Identifiers;

        $this->data = [
            'meta' => $this->meta->toArray(),
        ];

        $this->lineItems = [];
        $this->customData = [];
    }

    /**
     * Populate the model with data using setters
     *
     * @return $this
     */
    protected function populate(array $data): self
    {
        // Handle meta data
        if (isset($data['meta']) && is_array($data['meta'])) {
            $metaData = $data['meta'];
            // Ensure correct document_type
            $metaData['document_type'] = self::DOCUMENT_TYPE;

            $this->meta = Meta::fromArray($metaData);
            $this->data['meta'] = $this->meta->toArray();
        }

        // Handle basic fields
        if (isset($data['type'])) {
            $this->setType($data['type']);
        }

        if (isset($data['identifiers']) && is_array($data['identifiers'])) {
            $this->identifiers = Identifiers::fromArray($data['identifiers']);
            $this->data['identifiers'] = $this->identifiers->toArray();
        } elseif (isset($data['receipt_number'])) {
            // For backward compatibility
            $this->setReceiptNumber($data['receipt_number']);
        }

        if (isset($data['business_id'])) {
            $this->setBusinessId($data['business_id']);
        }

        if (isset($data['user_external_id'])) {
            $this->setUserExternalId($data['user_external_id']);
        }

        if (isset($data['sale_external_id'])) {
            $this->setSaleExternalId($data['sale_external_id']);
        }

        if (isset($data['date'])) {
            $this->setDate($data['date']);
        }

        if (isset($data['customer_number'])) {
            $this->setCustomerNumber($data['customer_number']);
        }

        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }

        if (isset($data['tax_mode'])) {
            $this->setTaxMode($data['tax_mode']);
        }

        if (isset($data['tax_type'])) {
            $this->setTaxType($data['tax_type']);
        }

        if (isset($data['subject'])) {
            $this->setSubject($data['subject']);
        }

        if (isset($data['closure'])) {
            $this->setClosure($data['closure']);
        }

        if (isset($data['notice'])) {
            $this->setNotice($data['notice']);
        }

        // Handle related receipts
        if (isset($data['related_receipts']) && is_array($data['related_receipts'])) {
            $this->setRelatedReceipts($data['related_receipts']);
        }

        if (isset($data['pursued_from']) && is_array($data['pursued_from'])) {
            $this->setPursuedFrom($data['pursued_from']);
        }

        // Handle line items
        if (isset($data['line_items']) && is_array($data['line_items'])) {
            foreach ($data['line_items'] as $lineItemData) {
                $this->addLineItem(LineItem::fromArray($lineItemData));
            }
        }

        // Handle custom data
        if (isset($data['custom_data']) && is_array($data['custom_data'])) {
            $this->customData = $data['custom_data'];
            $this->data['custom_data'] = $this->customData;
        }

        return $this;
    }

    /**
     * Set the API client for this receipt (required for sending)
     *
     * @return $this
     */
    public function setApiClient(AmeaxJsonImportApi $apiClient): self
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    /**
     * Set the receipt type
     *
     * @param  string  $type  The receipt type
     * @return $this
     */
    public function setType(string $type): self
    {
        $validTypes = [
            self::TYPE_OFFER,
            self::TYPE_ORDER,
            self::TYPE_INVOICE,
            self::TYPE_CREDIT_NOTE,
            self::TYPE_CANCELLATION,
        ];

        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Invalid receipt type. Valid types are: ' . implode(', ', $validTypes));
        }

        return $this->set('type', $type);
    }

    /**
     * Set the receipt number
     *
     * @param  string  $receiptNumber  The receipt number
     * @return $this
     */
    public function setReceiptNumber(string $receiptNumber): self
    {
        $this->identifiers->set('receipt_number', $receiptNumber);

        return $this->set('identifiers', $this->identifiers->toArray());
    }

    /**
     * Set the external ID
     *
     * @param  string|null  $externalId  The external ID or null to remove
     * @return $this
     */
    public function setExternalId(?string $externalId): self
    {
        $this->identifiers->setExternalId($externalId);

        return $this->set('identifiers', $this->identifiers->toArray());
    }

    /**
     * Set the Ameax internal ID
     *
     * @param  int|null  $ameaxInternalId  The Ameax internal ID or null to remove
     * @return $this
     */
    public function setAmeaxInternalId(?int $ameaxInternalId): self
    {
        $this->identifiers->setAmeaxInternalId($ameaxInternalId);

        return $this->set('identifiers', $this->identifiers->toArray());
    }

    /**
     * Set the business ID
     *
     * @param  int|null  $businessId  The business ID or null to remove
     * @return $this
     */
    public function setBusinessId(?int $businessId): self
    {
        if ($businessId === null) {
            return $this->remove('business_id');
        }

        return $this->set('business_id', $businessId);
    }

    /**
     * Set the user external ID
     *
     * @param  string|null  $userExternalId  The user external ID or null to remove
     * @return $this
     */
    public function setUserExternalId(?string $userExternalId): self
    {
        if ($userExternalId === null) {
            return $this->remove('user_external_id');
        }

        return $this->set('user_external_id', $userExternalId);
    }

    /**
     * Set the sale external ID
     *
     * @param  string|null  $saleExternalId  The sale external ID or null to remove
     * @return $this
     */
    public function setSaleExternalId(?string $saleExternalId): self
    {
        if ($saleExternalId === null) {
            return $this->remove('sale_external_id');
        }

        return $this->set('sale_external_id', $saleExternalId);
    }

    /**
     * Set the date
     *
     * @param  string|\DateTime|mixed  $date  The date (will be converted to YYYY-MM-DD format)
     * @return $this
     */
    public function setDate($date): self
    {
        // If DateTime object provided, convert to string
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d');
        } elseif (is_string($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            // Try to parse the date string if it's not in ISO format
            try {
                $dateObj = new \DateTime($date);
                $date = $dateObj->format('Y-m-d');
            } catch (\Exception $e) {
                // If we can't parse it, just pass it through
            }
        }

        return $this->set('date', $date);
    }

    /**
     * Set the customer number
     *
     * @param  string  $customerNumber  The customer number
     * @return $this
     */
    public function setCustomerNumber(string $customerNumber): self
    {
        return $this->set('customer_number', $customerNumber);
    }

    /**
     * Set the status
     *
     * @param  string  $status  The status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $validStatuses = [
            self::STATUS_DRAFT,
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Invalid status. Valid statuses are: ' . implode(', ', $validStatuses));
        }

        return $this->set('status', $status);
    }

    /**
     * Set the tax mode
     *
     * @param  string  $taxMode  The tax mode
     * @return $this
     */
    public function setTaxMode(string $taxMode): self
    {
        $validModes = [self::TAX_MODE_NET, self::TAX_MODE_GROSS];

        if (!in_array($taxMode, $validModes)) {
            throw new InvalidArgumentException('Invalid tax mode. Valid modes are: ' . implode(', ', $validModes));
        }

        return $this->set('tax_mode', $taxMode);
    }

    /**
     * Set the tax type
     *
     * @param  string  $taxType  The tax type
     * @return $this
     */
    public function setTaxType(string $taxType): self
    {
        $validTypes = [
            self::TAX_TYPE_REGULAR,
            self::TAX_TYPE_REDUCED,
            self::TAX_TYPE_EXEMPT_EU,
            self::TAX_TYPE_EXEMPT_THIRD,
            self::TAX_TYPE_EXEMPT_OTHER,
        ];

        if (!in_array($taxType, $validTypes)) {
            throw new InvalidArgumentException('Invalid tax type. Valid types are: ' . implode(', ', $validTypes));
        }

        return $this->set('tax_type', $taxType);
    }

    /**
     * Set the subject
     *
     * @param  string|null  $subject  The subject or null to remove
     * @return $this
     */
    public function setSubject(?string $subject): self
    {
        if ($subject === null) {
            return $this->remove('subject');
        }

        return $this->set('subject', $subject);
    }

    /**
     * Set the closure
     *
     * @param  string|null  $closure  The closure or null to remove
     * @return $this
     */
    public function setClosure(?string $closure): self
    {
        if ($closure === null) {
            return $this->remove('closure');
        }

        return $this->set('closure', $closure);
    }

    /**
     * Set the notice
     *
     * @param  string|null  $notice  The notice or null to remove
     * @return $this
     */
    public function setNotice(?string $notice): self
    {
        if ($notice === null) {
            return $this->remove('notice');
        }

        return $this->set('notice', $notice);
    }

    /**
     * Set related receipts
     *
     * @param  array|null  $relatedReceipts  Array of related receipt data or null to remove
     * @return $this
     */
    public function setRelatedReceipts(?array $relatedReceipts): self
    {
        if ($relatedReceipts === null) {
            return $this->remove('related_receipts');
        }

        return $this->set('related_receipts', $relatedReceipts);
    }

    /**
     * Set pursued from receipt
     *
     * @param  array|null  $pursuedFrom  Pursued from receipt data or null to remove
     * @return $this
     */
    public function setPursuedFrom(?array $pursuedFrom): self
    {
        if ($pursuedFrom === null) {
            return $this->remove('pursued_from');
        }

        return $this->set('pursued_from', $pursuedFrom);
    }

    /**
     * Add a line item
     *
     * @param  LineItem  $lineItem  The line item to add
     * @return $this
     */
    public function addLineItem(LineItem $lineItem): self
    {
        $this->lineItems[] = $lineItem;

        // Update the data array
        $lineItemsData = [];
        foreach ($this->lineItems as $item) {
            $lineItemsData[] = $item->toArray();
        }

        return $this->set('line_items', $lineItemsData);
    }

    /**
     * Clear all line items
     *
     * @return $this
     */
    public function clearLineItems(): self
    {
        $this->lineItems = [];

        return $this->set('line_items', []);
    }

    /**
     * Set a custom data field
     *
     * @param  string  $key  The field key
     * @param  mixed  $value  The field value or null to remove
     * @return $this
     */
    public function setCustomField(string $key, $value = null): self
    {
        if (!isset($this->data['custom_data'])) {
            $this->data['custom_data'] = [];
        }

        if ($value === null) {
            unset($this->customData[$key]);
            unset($this->data['custom_data'][$key]);

            if (empty($this->customData)) {
                $this->remove('custom_data');
            }

            return $this;
        }

        $this->customData[$key] = $value;
        $this->data['custom_data'][$key] = $value;

        return $this;
    }

    /**
     * Set custom data fields in bulk
     *
     * @param  array  $data  The custom data fields
     * @return $this
     */
    public function setCustomData(array $data): self
    {
        $this->customData = $data;
        $this->data['custom_data'] = $data;

        return $this;
    }

    /**
     * Get the type
     */
    public function getType(): ?string
    {
        return $this->get('type');
    }

    /**
     * Get the receipt number
     */
    public function getReceiptNumber(): ?string
    {
        return $this->identifiers->get('receipt_number');
    }

    /**
     * Get the external ID
     */
    public function getExternalId(): ?string
    {
        return $this->identifiers->getExternalId();
    }

    /**
     * Get the Ameax internal ID
     */
    public function getAmeaxInternalId(): ?int
    {
        return $this->identifiers->getAmeaxInternalId();
    }

    /**
     * Get the date
     */
    public function getDate(): ?string
    {
        return $this->get('date');
    }

    /**
     * Get the customer number
     */
    public function getCustomerNumber(): ?string
    {
        return $this->get('customer_number');
    }

    /**
     * Get the status
     */
    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    /**
     * Get the line items
     *
     * @return array<LineItem>
     */
    public function getLineItems(): array
    {
        return $this->lineItems;
    }

    /**
     * Get custom data fields
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Send the receipt to the Ameax API
     *
     * @return array The API response
     *
     * @throws InvalidArgumentException If no API client is set
     */
    public function sendToAmeax(): array
    {
        if (!$this->apiClient) {
            throw new InvalidArgumentException(
                'No API client set. Use setApiClient() before calling sendToAmeax().'
            );
        }

        return $this->apiClient->send($this->data);
    }
}
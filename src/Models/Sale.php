<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use InvalidArgumentException;

class Sale extends BaseModel
{
    public const DOCUMENT_TYPE = 'ameax_sale';

    public const SCHEMA_VERSION = '1.0';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const SELLING_STATUS_IDENTIFICATION = 'identification';
    public const SELLING_STATUS_ACQUISITION = 'acquisition';
    public const SELLING_STATUS_QUALIFICATION = 'qualification';
    public const SELLING_STATUS_PROPOSAL = 'proposal';
    public const SELLING_STATUS_SALE = 'sale';

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
     * @var Rating|null The rating
     */
    protected ?Rating $rating = null;

    /**
     * @var array Custom data fields
     */
    protected array $customData = [];

    /**
     * Constructor initializes a new sale with meta information
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

        // Handle identifiers
        if (isset($data['identifiers']) && is_array($data['identifiers'])) {
            $this->identifiers = Identifiers::fromArray($data['identifiers']);
            $this->data['identifiers'] = $this->identifiers->toArray();
        } elseif (isset($data['external_id'])) {
            // For backward compatibility
            $this->setExternalId($data['external_id']);
        }

        // Handle customer
        if (isset($data['customer']) && is_array($data['customer'])) {
            $this->setCustomer($data['customer']);
        }

        // Handle basic fields
        if (isset($data['subject'])) {
            $this->setSubject($data['subject']);
        }

        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }

        if (isset($data['sale_status'])) {
            $this->setSaleStatus($data['sale_status']);
        }

        if (isset($data['selling_status'])) {
            $this->setSellingStatus($data['selling_status']);
        }

        if (isset($data['user_external_id'])) {
            $this->setUserExternalId($data['user_external_id']);
        }

        if (isset($data['date'])) {
            $this->setDate($data['date']);
        }

        if (isset($data['amount'])) {
            $this->setAmount($data['amount']);
        }

        if (isset($data['probability'])) {
            $this->setProbability($data['probability']);
        }

        if (isset($data['close_date'])) {
            $this->setCloseDate($data['close_date']);
        }

        // Handle rating
        if (isset($data['rating']) && is_array($data['rating'])) {
            $this->rating = Rating::fromArray($data['rating']);
            $this->data['rating'] = $this->rating->toArray();
        }

        // Handle custom data
        if (isset($data['custom_data']) && is_array($data['custom_data'])) {
            $this->customData = $data['custom_data'];
            $this->data['custom_data'] = $this->customData;
        }

        return $this;
    }

    /**
     * Set the API client for this sale (required for sending)
     *
     * @return $this
     */
    public function setApiClient(AmeaxJsonImportApi $apiClient): self
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    /**
     * Set the external ID
     *
     * @param  string  $externalId  The external ID
     * @return $this
     */
    public function setExternalId(string $externalId): self
    {
        $this->identifiers->set('external_id', $externalId);

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
     * Set the customer reference
     *
     * @param  array  $customer  The customer data with either customer_number or external_id
     * @return $this
     */
    public function setCustomer(array $customer): self
    {
        if (!isset($customer['customer_number']) && !isset($customer['external_id'])) {
            throw new InvalidArgumentException('Customer must have either customer_number or external_id');
        }

        return $this->set('customer', $customer);
    }

    /**
     * Set the customer by customer number
     *
     * @param  string  $customerNumber  The customer number
     * @return $this
     */
    public function setCustomerNumber(string $customerNumber): self
    {
        return $this->setCustomer(['customer_number' => $customerNumber]);
    }

    /**
     * Set the customer by external ID
     *
     * @param  string  $externalId  The customer external ID
     * @return $this
     */
    public function setCustomerExternalId(string $externalId): self
    {
        return $this->setCustomer(['external_id' => $externalId]);
    }

    /**
     * Set the subject
     *
     * @param  string  $subject  The subject
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        return $this->set('subject', $subject);
    }

    /**
     * Set the description
     *
     * @param  string|null  $description  The description or null to remove
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        if ($description === null) {
            return $this->remove('description');
        }

        return $this->set('description', $description);
    }

    /**
     * Set the sale status
     *
     * @param  string  $status  The sale status
     * @return $this
     */
    public function setSaleStatus(string $status): self
    {
        $validStatuses = [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Invalid sale status. Valid statuses are: ' . implode(', ', $validStatuses));
        }

        return $this->set('sale_status', $status);
    }

    /**
     * Set the selling status
     *
     * @param  string  $status  The selling status
     * @return $this
     */
    public function setSellingStatus(string $status): self
    {
        $validStatuses = [
            self::SELLING_STATUS_IDENTIFICATION,
            self::SELLING_STATUS_ACQUISITION,
            self::SELLING_STATUS_QUALIFICATION,
            self::SELLING_STATUS_PROPOSAL,
            self::SELLING_STATUS_SALE,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Invalid selling status. Valid statuses are: ' . implode(', ', $validStatuses));
        }

        return $this->set('selling_status', $status);
    }

    /**
     * Set the user external ID
     *
     * @param  string  $userExternalId  The user external ID
     * @return $this
     */
    public function setUserExternalId(string $userExternalId): self
    {
        return $this->set('user_external_id', $userExternalId);
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
     * Set the amount
     *
     * @param  float|int  $amount  The amount
     * @return $this
     */
    public function setAmount($amount): self
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Amount must be numeric.');
        }

        // Round to 2 decimal places
        $amount = round((float) $amount, 2);

        return $this->set('amount', $amount);
    }

    /**
     * Set the probability
     *
     * @param  int  $probability  The probability (0-100)
     * @return $this
     */
    public function setProbability(int $probability): self
    {
        if ($probability < 0 || $probability > 100) {
            throw new InvalidArgumentException('Probability must be between 0 and 100.');
        }

        return $this->set('probability', $probability);
    }

    /**
     * Set the close date
     *
     * @param  string|\DateTime|mixed  $closeDate  The close date (will be converted to YYYY-MM-DD format)
     * @return $this
     */
    public function setCloseDate($closeDate): self
    {
        // If DateTime object provided, convert to string
        if ($closeDate instanceof \DateTime) {
            $closeDate = $closeDate->format('Y-m-d');
        } elseif (is_string($closeDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $closeDate)) {
            // Try to parse the date string if it's not in ISO format
            try {
                $dateObj = new \DateTime($closeDate);
                $closeDate = $dateObj->format('Y-m-d');
            } catch (\Exception $e) {
                // If we can't parse it, just pass it through
            }
        }

        return $this->set('close_date', $closeDate);
    }

    /**
     * Create and set rating
     *
     * @return Rating
     */
    public function createRating(): Rating
    {
        $this->rating = new Rating;
        $this->data['rating'] = $this->rating->toArray();

        return $this->rating;
    }

    /**
     * Set the rating
     *
     * @param  Rating  $rating  The rating
     * @return $this
     */
    public function setRating(Rating $rating): self
    {
        $this->rating = $rating;

        return $this->set('rating', $rating->toArray());
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
     * Get the external ID
     */
    public function getExternalId(): ?string
    {
        return $this->identifiers->get('external_id');
    }

    /**
     * Get the Ameax internal ID
     */
    public function getAmeaxInternalId(): ?int
    {
        return $this->identifiers->getAmeaxInternalId();
    }

    /**
     * Get the customer
     */
    public function getCustomer(): ?array
    {
        return $this->get('customer');
    }

    /**
     * Get the subject
     */
    public function getSubject(): ?string
    {
        return $this->get('subject');
    }

    /**
     * Get the description
     */
    public function getDescription(): ?string
    {
        return $this->get('description');
    }

    /**
     * Get the sale status
     */
    public function getSaleStatus(): ?string
    {
        return $this->get('sale_status');
    }

    /**
     * Get the selling status
     */
    public function getSellingStatus(): ?string
    {
        return $this->get('selling_status');
    }

    /**
     * Get the user external ID
     */
    public function getUserExternalId(): ?string
    {
        return $this->get('user_external_id');
    }

    /**
     * Get the date
     */
    public function getDate(): ?string
    {
        return $this->get('date');
    }

    /**
     * Get the amount
     */
    public function getAmount(): ?float
    {
        return $this->get('amount');
    }

    /**
     * Get the probability
     */
    public function getProbability(): ?int
    {
        return $this->get('probability');
    }

    /**
     * Get the close date
     */
    public function getCloseDate(): ?string
    {
        return $this->get('close_date');
    }

    /**
     * Get the rating
     */
    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    /**
     * Get custom data fields
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Send the sale to the Ameax API
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
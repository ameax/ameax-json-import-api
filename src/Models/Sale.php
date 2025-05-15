<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi;
use InvalidArgumentException;

class Sale extends BaseModel
{
    protected ?AmeaxJsonImportApi $apiClient = null;

    /**
     * Create a new sale instance
     */
    public function __construct()
    {
        $this->data = [
            'meta' => [
                'document_type' => Meta::DOCUMENT_TYPE_SALE,
                'schema_version' => Meta::SCHEMA_VERSION,
            ],
        ];
    }

    /**
     * Populate the model with data using setters
     *
     * @return $this
     */
    protected function populate(array $data): self
    {
        // Handle meta
        if (isset($data['meta'])) {
            if (! isset($this->data['meta'])) {
                $this->data['meta'] = [];
            }

            $this->data['meta'] = array_merge($this->data['meta'], $data['meta']);

            // Ensure document type is correct
            $this->data['meta']['document_type'] = Meta::DOCUMENT_TYPE_SALE;
        }

        // Handle identifiers
        if (isset($data['identifiers']) && is_array($data['identifiers'])) {
            if (isset($data['identifiers']['external_id'])) {
                $this->setExternalId($data['identifiers']['external_id']);
            }
        }

        // Handle customer information
        if (isset($data['customer']) && is_array($data['customer'])) {
            if (isset($data['customer']['customer_number'])) {
                $this->setCustomerNumber($data['customer']['customer_number']);
            }
            if (isset($data['customer']['external_id'])) {
                $this->setCustomerExternalId($data['customer']['external_id']);
            }
        }

        // Handle basic sale information
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

        // Handle ratings
        if (isset($data['rating']) && is_array($data['rating'])) {
            $ratingFields = ['relationship', 'proposition', 'trust', 'competition',
                'need_for_action', 'buying_process', 'price'];

            foreach ($ratingFields as $field) {
                if (isset($data['rating'][$field]) && is_array($data['rating'][$field])) {
                    $rating = $data['rating'][$field];
                    if (isset($rating['rating']) && isset($rating['source'])) {
                        $this->setRating($field, $rating['rating'], $rating['source']);
                    }
                }
            }
        }

        // Handle custom data
        if (isset($data['custom_data']) && is_array($data['custom_data'])) {
            $this->setCustomData($data['custom_data']);
        }

        return $this;
    }

    /**
     * Set the API client for this sale
     *
     * @param  AmeaxJsonImportApi  $client  The API client
     * @return $this
     */
    public function setApiClient(AmeaxJsonImportApi $client): self
    {
        $this->apiClient = $client;

        return $this;
    }

    /**
     * Set the external ID for this sale
     *
     * @param  string  $externalId  The external ID
     * @return $this
     */
    public function setExternalId(string $externalId): self
    {
        if (! isset($this->data['identifiers'])) {
            $this->data['identifiers'] = [];
        }

        return $this->set('identifiers.external_id', $externalId);
    }

    /**
     * Set the customer number for this sale
     *
     * @param  string  $customerNumber  The customer number
     * @return $this
     */
    public function setCustomerNumber(string $customerNumber): self
    {
        if (! isset($this->data['customer'])) {
            $this->data['customer'] = [];
        }

        return $this->set('customer.customer_number', $customerNumber);
    }

    /**
     * Set the customer external ID for this sale
     *
     * @param  string  $externalId  The customer external ID
     * @return $this
     */
    public function setCustomerExternalId(string $externalId): self
    {
        if (! isset($this->data['customer'])) {
            $this->data['customer'] = [];
        }

        return $this->set('customer.external_id', $externalId);
    }

    /**
     * Set the subject for this sale
     *
     * @param  string  $subject  The sale subject
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        return $this->set('subject', $subject);
    }

    /**
     * Set the description for this sale
     *
     * @param  string|null  $description  The sale description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        return $this->set('description', $description);
    }

    /**
     * Set the sale status
     *
     * @param  string  $status  The sale status (active, inactive, completed, cancelled)
     * @return $this
     *
     * @throws InvalidArgumentException If the status is invalid
     */
    public function setSaleStatus(string $status): self
    {
        $validStatuses = ['active', 'inactive', 'completed', 'cancelled'];
        if (! in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Sale status must be one of: '.implode(', ', $validStatuses));
        }

        return $this->set('sale_status', $status);
    }

    /**
     * Set the selling status
     *
     * @param  string  $status  The selling status (identification, acquisition, qualification, proposal, sale)
     * @return $this
     *
     * @throws InvalidArgumentException If the status is invalid
     */
    public function setSellingStatus(string $status): self
    {
        $validStatuses = ['identification', 'acquisition', 'qualification', 'proposal', 'sale'];
        if (! in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Selling status must be one of: '.implode(', ', $validStatuses));
        }

        return $this->set('selling_status', $status);
    }

    /**
     * Set the user external ID
     *
     * @param  string  $userId  The user external ID
     * @return $this
     */
    public function setUserExternalId(string $userId): self
    {
        return $this->set('user_external_id', $userId);
    }

    /**
     * Set the date for this sale
     *
     * @param  string  $date  The date (YYYY-MM-DD)
     * @return $this
     */
    public function setDate(string $date): self
    {
        return $this->set('date', $date);
    }

    /**
     * Set the amount for this sale
     *
     * @param  float  $amount  The sale amount
     * @return $this
     *
     * @throws InvalidArgumentException If the amount is negative
     */
    public function setAmount(float $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        return $this->set('amount', $amount);
    }

    /**
     * Set the probability for this sale
     *
     * @param  int  $probability  The sale probability (0-100)
     * @return $this
     *
     * @throws InvalidArgumentException If the probability is not between 0 and 100
     */
    public function setProbability(int $probability): self
    {
        if ($probability < 0 || $probability > 100) {
            throw new InvalidArgumentException('Probability must be between 0 and 100');
        }

        return $this->set('probability', $probability);
    }

    /**
     * Set the close date for this sale
     *
     * @param  string  $closeDate  The close date (YYYY-MM-DD)
     * @return $this
     */
    public function setCloseDate(string $closeDate): self
    {
        return $this->set('close_date', $closeDate);
    }

    /**
     * Set a rating for this sale
     *
     * @param  string  $category  The rating category (relationship, proposition, trust, competition, need_for_action, buying_process, price)
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The rating source (known, assumed, guessed)
     * @return $this
     *
     * @throws InvalidArgumentException If the category, rating, or source is invalid
     */
    public function setRating(string $category, int $rating, string $source): self
    {
        $validCategories = ['relationship', 'proposition', 'trust', 'competition',
            'need_for_action', 'buying_process', 'price'];

        if (! in_array($category, $validCategories)) {
            throw new InvalidArgumentException('Rating category must be one of: '.implode(', ', $validCategories));
        }

        if ($rating < 1 || $rating > 7) {
            throw new InvalidArgumentException('Rating must be between 1 and 7');
        }

        $validSources = ['known', 'assumed', 'guessed'];
        if (! in_array($source, $validSources)) {
            throw new InvalidArgumentException('Rating source must be one of: '.implode(', ', $validSources));
        }

        if (! isset($this->data['rating'])) {
            $this->data['rating'] = [];
        }

        $this->set('rating.'.$category.'.rating', $rating);
        $this->set('rating.'.$category.'.source', $source);

        return $this;
    }

    /**
     * Set custom data for this sale
     *
     * @param  array  $data  The custom data
     * @return $this
     */
    public function setCustomData(array $data): self
    {
        return $this->set('custom_data', $data);
    }

    /**
     * Set a single custom field for this sale
     *
     * @param  string  $key  The custom field key
     * @param  mixed  $value  The custom field value
     * @return $this
     */
    public function setCustomField(string $key, mixed $value): self
    {
        if (! isset($this->data['custom_data'])) {
            $this->data['custom_data'] = [];
        }

        return $this->set('custom_data.'.$key, $value);
    }

    /**
     * Send this sale data to Ameax
     *
     * @return array The API response
     *
     * @throws \Exception If API client is not set or request fails
     */
    public function sendToAmeax(): array
    {
        if (! $this->apiClient) {
            throw new \Exception('API client not set. Use setApiClient() before sending data.');
        }

        return $this->apiClient->sendSale($this->toArray());
    }
}

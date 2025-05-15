<?php

namespace Ameax\AmeaxJsonImportApi\Schema;

class SaleSchema
{
    public const DOCUMENT_TYPE = 'ameax_sale';

    public const SCHEMA_VERSION = '1.0';

    /**
     * Create a new sale schema with required fields
     *
     * @param string $externalId The external ID for this sale
     * @param string $subject The subject of the sale
     * @param string $saleStatus The sale status (active, inactive, completed, cancelled)
     * @param string $sellingStatus The selling status (identification, acquisition, qualification, proposal, sale)
     * @param string $userExternalId The user external ID
     * @param string $date The date (YYYY-MM-DD)
     * @param float $amount The sale amount
     * @param int $probability The sale probability (0-100)
     * @param string $closeDate The close date (YYYY-MM-DD)
     */
    public static function create(
        string $externalId,
        string $subject,
        string $saleStatus,
        string $sellingStatus,
        string $userExternalId,
        string $date,
        float $amount,
        int $probability,
        string $closeDate
    ): array {
        return [
            'meta' => [
                'document_type' => self::DOCUMENT_TYPE,
                'schema_version' => self::SCHEMA_VERSION,
            ],
            'identifiers' => [
                'external_id' => $externalId,
            ],
            'subject' => $subject,
            'sale_status' => $saleStatus,
            'selling_status' => $sellingStatus,
            'user_external_id' => $userExternalId,
            'date' => $date,
            'amount' => $amount,
            'probability' => $probability,
            'close_date' => $closeDate,
        ];
    }

    /**
     * Set customer information by customer number
     *
     * @param array $sale Sale data
     * @param string $customerNumber Customer number
     */
    public static function setCustomerNumber(array $sale, string $customerNumber): array
    {
        if (!isset($sale['customer'])) {
            $sale['customer'] = [];
        }
        
        $sale['customer']['customer_number'] = $customerNumber;
        
        return $sale;
    }

    /**
     * Set customer information by external ID
     *
     * @param array $sale Sale data
     * @param string $externalId Customer external ID
     */
    public static function setCustomerExternalId(array $sale, string $externalId): array
    {
        if (!isset($sale['customer'])) {
            $sale['customer'] = [];
        }
        
        $sale['customer']['external_id'] = $externalId;
        
        return $sale;
    }

    /**
     * Add a rating to the sale
     *
     * @param array $sale Sale data
     * @param string $category The rating category
     * @param int $rating The rating value (1-7)
     * @param string $source The rating source (known, assumed, guessed)
     */
    public static function addRating(array $sale, string $category, int $rating, string $source): array
    {
        if (!isset($sale['rating'])) {
            $sale['rating'] = [];
        }
        
        $sale['rating'][$category] = [
            'rating' => $rating,
            'source' => $source,
        ];
        
        return $sale;
    }
}
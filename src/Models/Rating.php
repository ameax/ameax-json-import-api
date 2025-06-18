<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use InvalidArgumentException;

class Rating extends BaseModel
{
    public const SOURCE_KNOWN = 'known';

    public const SOURCE_ASSUMED = 'assumed';

    public const SOURCE_GUESSED = 'guessed';

    /**
     * Rating categories
     */
    public const CATEGORY_RELATIONSHIP = 'relationship';

    public const CATEGORY_PROPOSITION = 'proposition';

    public const CATEGORY_TRUST = 'trust';

    public const CATEGORY_COMPETITION = 'competition';

    public const CATEGORY_NEED_FOR_ACTION = 'need_for_action';

    public const CATEGORY_BUYING_PROCESS = 'buying_process';

    public const CATEGORY_PRICE = 'price';

    /**
     * Constructor initializes an empty rating
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Populate the model with data using setters
     *
     * @param  array<string, mixed>  $data
     * @return $this
     */
    protected function populate(array $data): self
    {
        $categories = [
            self::CATEGORY_RELATIONSHIP,
            self::CATEGORY_PROPOSITION,
            self::CATEGORY_TRUST,
            self::CATEGORY_COMPETITION,
            self::CATEGORY_NEED_FOR_ACTION,
            self::CATEGORY_BUYING_PROCESS,
            self::CATEGORY_PRICE,
        ];

        foreach ($categories as $category) {
            if (isset($data[$category]) && is_array($data[$category])) {
                $this->setRatingItem($category, $data[$category]['rating'], $data[$category]['source']);
            }
        }

        return $this;
    }

    /**
     * Set a rating item
     *
     * @param  string  $category  The rating category
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setRatingItem(string $category, int $rating, string $source): self
    {
        $validCategories = [
            self::CATEGORY_RELATIONSHIP,
            self::CATEGORY_PROPOSITION,
            self::CATEGORY_TRUST,
            self::CATEGORY_COMPETITION,
            self::CATEGORY_NEED_FOR_ACTION,
            self::CATEGORY_BUYING_PROCESS,
            self::CATEGORY_PRICE,
        ];

        if (! in_array($category, $validCategories)) {
            throw new InvalidArgumentException('Invalid rating category. Valid categories are: '.implode(', ', $validCategories));
        }

        if ($rating < 1 || $rating > 7) {
            throw new InvalidArgumentException('Rating must be between 1 and 7.');
        }

        $validSources = [self::SOURCE_KNOWN, self::SOURCE_ASSUMED, self::SOURCE_GUESSED];

        if (! in_array($source, $validSources)) {
            throw new InvalidArgumentException('Invalid rating source. Valid sources are: '.implode(', ', $validSources));
        }

        return $this->set($category, [
            'rating' => $rating,
            'source' => $source,
        ]);
    }

    /**
     * Set the relationship rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setRelationship(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_RELATIONSHIP, $rating, $source);
    }

    /**
     * Set the proposition rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setProposition(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_PROPOSITION, $rating, $source);
    }

    /**
     * Set the trust rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setTrust(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_TRUST, $rating, $source);
    }

    /**
     * Set the competition rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setCompetition(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_COMPETITION, $rating, $source);
    }

    /**
     * Set the need for action rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setNeedForAction(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_NEED_FOR_ACTION, $rating, $source);
    }

    /**
     * Set the buying process rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setBuyingProcess(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_BUYING_PROCESS, $rating, $source);
    }

    /**
     * Set the price rating
     *
     * @param  int  $rating  The rating value (1-7)
     * @param  string  $source  The source of the rating
     * @return $this
     */
    public function setPrice(int $rating, string $source): self
    {
        return $this->setRatingItem(self::CATEGORY_PRICE, $rating, $source);
    }

    /**
     * Get a rating item
     *
     * @param  string  $category  The rating category
     * @return array{rating: int, source: string}|null The rating data or null if not set
     */
    public function getRatingItem(string $category): ?array
    {
        return $this->get($category);
    }

    /**
     * Get the relationship rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getRelationship(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_RELATIONSHIP);
    }

    /**
     * Get the proposition rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getProposition(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_PROPOSITION);
    }

    /**
     * Get the trust rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getTrust(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_TRUST);
    }

    /**
     * Get the competition rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getCompetition(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_COMPETITION);
    }

    /**
     * Get the need for action rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getNeedForAction(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_NEED_FOR_ACTION);
    }

    /**
     * Get the buying process rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getBuyingProcess(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_BUYING_PROCESS);
    }

    /**
     * Get the price rating
     *
     * @return array{rating: int, source: string}|null
     */
    public function getPrice(): ?array
    {
        return $this->getRatingItem(self::CATEGORY_PRICE);
    }

    /**
     * Calculate the average rating across all categories
     *
     * @return float|null The average rating or null if no ratings are set
     */
    public function getAverageRating(): ?float
    {
        $categories = [
            self::CATEGORY_RELATIONSHIP,
            self::CATEGORY_PROPOSITION,
            self::CATEGORY_TRUST,
            self::CATEGORY_COMPETITION,
            self::CATEGORY_NEED_FOR_ACTION,
            self::CATEGORY_BUYING_PROCESS,
            self::CATEGORY_PRICE,
        ];

        $total = 0;
        $count = 0;

        foreach ($categories as $category) {
            $item = $this->getRatingItem($category);
            if ($item !== null && isset($item['rating'])) {
                $total += $item['rating'];
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        return round($total / $count, 2);
    }

    /**
     * Check if all required ratings are set
     */
    public function isComplete(): bool
    {
        $categories = [
            self::CATEGORY_RELATIONSHIP,
            self::CATEGORY_PROPOSITION,
            self::CATEGORY_TRUST,
            self::CATEGORY_COMPETITION,
            self::CATEGORY_NEED_FOR_ACTION,
            self::CATEGORY_BUYING_PROCESS,
            self::CATEGORY_PRICE,
        ];

        foreach ($categories as $category) {
            if ($this->getRatingItem($category) === null) {
                return false;
            }
        }

        return true;
    }
}

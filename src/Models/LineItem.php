<?php

namespace Ameax\AmeaxJsonImportApi\Models;

use InvalidArgumentException;

class LineItem extends BaseModel
{
    public const DISCOUNT_TYPE_PERCENT = 'percent';
    public const DISCOUNT_TYPE_AMOUNT = 'amount';

    public const TAX_TYPE_REGULAR = 'regular';
    public const TAX_TYPE_REDUCED = 'reduced';
    public const TAX_TYPE_EXEMPT = 'exempt';

    /**
     * Constructor initializes an empty line item
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Populate the model with data using setters
     *
     * @param array<string, mixed> $data
     * @return $this
     */
    protected function populate(array $data): self
    {
        if (isset($data['article_number'])) {
            $this->setArticleNumber($data['article_number']);
        }

        if (isset($data['category'])) {
            $this->setCategory($data['category']);
        }

        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }

        if (isset($data['quantity'])) {
            $this->setQuantity($data['quantity']);
        }

        if (isset($data['price'])) {
            $this->setPrice($data['price']);
        }

        if (isset($data['discount'])) {
            $this->setDiscount($data['discount']);
        }

        if (isset($data['discount_type'])) {
            $this->setDiscountType($data['discount_type']);
        }

        if (isset($data['tax_rate'])) {
            $this->setTaxRate($data['tax_rate']);
        }

        if (isset($data['tax_type'])) {
            $this->setTaxType($data['tax_type']);
        }

        return $this;
    }

    /**
     * Set the article number
     *
     * @param  string|null  $articleNumber  The article number or null to remove
     * @return $this
     */
    public function setArticleNumber(?string $articleNumber): self
    {
        if ($articleNumber === null) {
            return $this->remove('article_number');
        }

        return $this->set('article_number', $articleNumber);
    }

    /**
     * Set the category
     *
     * @param  string|null  $category  The category or null to remove
     * @return $this
     */
    public function setCategory(?string $category): self
    {
        if ($category === null) {
            return $this->remove('category');
        }

        return $this->set('category', $category);
    }

    /**
     * Set the description
     *
     * @param  string  $description  The description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        return $this->set('description', $description);
    }

    /**
     * Set the quantity
     *
     * @param  float|int  $quantity  The quantity
     * @return $this
     */
    public function setQuantity($quantity): self
    {
        if (!is_numeric($quantity)) {
            throw new InvalidArgumentException('Quantity must be numeric.');
        }

        return $this->set('quantity', (float) $quantity);
    }

    /**
     * Set the price
     *
     * @param  float|int  $price  The price
     * @return $this
     */
    public function setPrice($price): self
    {
        if (!is_numeric($price)) {
            throw new InvalidArgumentException('Price must be numeric.');
        }

        return $this->set('price', (float) $price);
    }

    /**
     * Set the discount
     *
     * @param  float|int|null  $discount  The discount or null to remove
     * @return $this
     */
    public function setDiscount($discount): self
    {
        if ($discount === null) {
            return $this->remove('discount');
        }

        if (!is_numeric($discount)) {
            throw new InvalidArgumentException('Discount must be numeric.');
        }

        return $this->set('discount', (float) $discount);
    }

    /**
     * Set the discount type
     *
     * @param  string|null  $discountType  The discount type or null to remove
     * @return $this
     */
    public function setDiscountType(?string $discountType): self
    {
        if ($discountType === null) {
            return $this->remove('discount_type');
        }

        $validTypes = [self::DISCOUNT_TYPE_PERCENT, self::DISCOUNT_TYPE_AMOUNT];

        if (!in_array($discountType, $validTypes)) {
            throw new InvalidArgumentException('Invalid discount type. Valid types are: ' . implode(', ', $validTypes));
        }

        return $this->set('discount_type', $discountType);
    }

    /**
     * Set the tax rate
     *
     * @param  float|int  $taxRate  The tax rate
     * @return $this
     */
    public function setTaxRate($taxRate): self
    {
        if (!is_numeric($taxRate)) {
            throw new InvalidArgumentException('Tax rate must be numeric.');
        }

        return $this->set('tax_rate', (float) $taxRate);
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
            self::TAX_TYPE_EXEMPT,
        ];

        if (!in_array($taxType, $validTypes)) {
            throw new InvalidArgumentException('Invalid tax type. Valid types are: ' . implode(', ', $validTypes));
        }

        return $this->set('tax_type', $taxType);
    }

    /**
     * Get the article number
     */
    public function getArticleNumber(): ?string
    {
        return $this->get('article_number');
    }

    /**
     * Get the category
     */
    public function getCategory(): ?string
    {
        return $this->get('category');
    }

    /**
     * Get the description
     */
    public function getDescription(): ?string
    {
        return $this->get('description');
    }

    /**
     * Get the quantity
     */
    public function getQuantity(): ?float
    {
        return $this->get('quantity');
    }

    /**
     * Get the price
     */
    public function getPrice(): ?float
    {
        return $this->get('price');
    }

    /**
     * Get the discount
     */
    public function getDiscount(): ?float
    {
        return $this->get('discount');
    }

    /**
     * Get the discount type
     */
    public function getDiscountType(): ?string
    {
        return $this->get('discount_type');
    }

    /**
     * Get the tax rate
     */
    public function getTaxRate(): ?float
    {
        return $this->get('tax_rate');
    }

    /**
     * Get the tax type
     */
    public function getTaxType(): ?string
    {
        return $this->get('tax_type');
    }

    /**
     * Calculate the line total (quantity * price - discount)
     */
    public function getLineTotal(): float
    {
        $quantity = $this->getQuantity() ?? 0;
        $price = $this->getPrice() ?? 0;
        $discount = $this->getDiscount() ?? 0;
        $discountType = $this->getDiscountType();

        $subtotal = $quantity * $price;

        if ($discount > 0) {
            if ($discountType === self::DISCOUNT_TYPE_PERCENT) {
                $subtotal -= ($subtotal * $discount / 100);
            } else {
                $subtotal -= $discount;
            }
        }

        return round($subtotal, 2);
    }

    /**
     * Calculate the tax amount
     */
    public function getTaxAmount(): float
    {
        $lineTotal = $this->getLineTotal();
        $taxRate = $this->getTaxRate() ?? 0;

        return round($lineTotal * $taxRate / 100, 2);
    }

    /**
     * Calculate the total including tax
     */
    public function getTotalWithTax(): float
    {
        return $this->getLineTotal() + $this->getTaxAmount();
    }
}
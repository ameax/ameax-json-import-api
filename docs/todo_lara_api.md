# TODO: Laravel API Updates for New Schema v1.0

This document lists the new fields and document types that need to be handled in the Ameax Laravel API backend.

## New Fields in Existing Models

### 1. `ameax_internal_id` Field

The `ameax_internal_id` field has been added to identifiers in the following document types:

- `ameax_organization_account` - in main identifiers and contact identifiers
- `ameax_private_person_account` - in main identifiers  
- `ameax_receipt` - in main identifiers
- `ameax_sale` - in main identifiers

**Field specifications:**
- Type: `integer` or `null`
- Purpose: Internal Ameax ID reference
- Should be read-only from client perspective (set by API only)

### 2. Private Person Identifiers

Private person accounts now support an `identifiers` object (was previously only `customer_number` at root level):
- `customer_number` - moved inside identifiers object
- `external_id` - new field
- `ameax_internal_id` - new field

## New Document Types

### 1. Receipt Documents (`ameax_receipt`)

New document type for handling invoices, orders, offers, credit notes, and cancellations.

**Key fields to handle:**
- `type` - enum: offer, order, invoice, credit_note, cancellation_document
- `identifiers.receipt_number` - required, unique receipt number
- `identifiers.external_id` - optional external reference
- `identifiers.ameax_internal_id` - internal ID
- `business_id` - optional business reference
- `user_external_id` - external user reference
- `sale_external_id` - link to sales opportunity
- `status` - enum: draft, pending, completed, cancelled
- `tax_mode` - enum: net, gross
- `tax_type` - enum: regular, reduced, exempt_eu, exempt_third, exempt_other
- `line_items` - array of line items with tax calculations
- `related_receipts` - array of related receipt references
- `pursued_from` - reference to original receipt (for credit notes, etc.)

**Line item structure:**
- `article_number` - optional
- `category` - optional
- `description` - required
- `quantity` - required number
- `price` - required number
- `discount` - optional number
- `discount_type` - enum: percent, amount (only if discount is set)
- `tax_rate` - required number
- `tax_type` - enum: regular, reduced, exempt

### 2. Sales/Opportunity Documents (`ameax_sale`)

New document type for tracking sales opportunities.

**Key fields to handle:**
- `identifiers.external_id` - required external reference
- `identifiers.ameax_internal_id` - internal ID
- `customer` - object with either `customer_number` OR `external_id` (one required)
- `subject` - required description
- `description` - optional detailed description
- `sale_status` - enum: active, inactive, completed, cancelled
- `selling_status` - enum: identification, acquisition, qualification, proposal, sale
- `user_external_id` - required sales rep reference
- `date` - required date of opportunity
- `amount` - required monetary value (2 decimals)
- `probability` - required integer 0-100
- `close_date` - required expected close date
- `rating` - optional rating object with 7 categories

**Rating structure (all categories required if rating is provided):**
Each category has:
- `rating` - integer 1-7
- `source` - enum: known, assumed, guessed

Categories:
- `relationship`
- `proposition`
- `trust`
- `competition`
- `need_for_action`
- `buying_process`
- `price`

## Validation Requirements

1. **Receipt Relations**: When `related_receipts` or `pursued_from` reference other receipts, validate that either `receipt_number` or `external_id` is provided and the referenced receipt exists.

2. **Customer References**: In sales documents, validate that the customer exists when referenced by `customer_number` or `external_id`.

3. **User References**: Validate that `user_external_id` references a valid user in both receipts and sales.

4. **Date Formats**: All dates must be in ISO format (YYYY-MM-DD).

5. **Amount Precision**: The `amount` field in sales must be stored with exactly 2 decimal places.

## Migration Considerations

1. Existing private person records need to migrate `customer_number` from root level into the `identifiers` object.

2. Consider adding indexes for the new `ameax_internal_id` fields for efficient lookups.

3. The new document types will need their own database tables with appropriate foreign key constraints.
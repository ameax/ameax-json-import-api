# Changelog

All notable changes to `ameax-json-import-api` will be documented in this file.

## 1.1.1 - 2025-06-01

### Added
- Complete documentation for Receipt and Sale models
- Examples for all new document types in README

### Fixed
- PHPStan level 6 compliance with proper array type annotations
- Added missing sendReceipt and sendSale methods to API client
- Improved type safety with array shape specifications

## 1.1.0 - 2025-06-01

### Added
- Support for new JSON schema v1.0 with `ameax_internal_id` field
- New Receipt model for handling invoices, orders, offers, credit notes, and cancellations
- New LineItem model with tax calculation support for receipts
- New Sale model for tracking sales opportunities
- New Rating model with 7 rating categories for sales evaluation
- External ID and Ameax internal ID support in PrivatePerson model
- Documentation for Laravel API backend requirements (`docs/todo_lara_api.md`)

### Changed
- Organization schema renamed to include v1-0 version suffix
- Private person now supports identifiers object structure
- All document types now include `ameax_internal_id` in identifiers

## v1.0.1 - 2025-05-13

**Full Changelog**: https://github.com/ameax/ameax-json-import-api/commits/v1.0.1

## 1.0.1 - 2025-05-13

### Added

- Validation for customer_number field to ensure it's a numerical string or null

### Fixed

- Empty customer_number strings now properly convert to null

## 1.0.0 - 2025-05-13

### Added

- Initial release with support for Organization and PrivatePerson models
- Fluent interface for building API requests
- Comprehensive test suite for all functionality
- Type declarations and strict typing throughout the codebase
- PHPStan level 6 static analysis compliance
- Documentation for all public methods and classes
- Examples for common use cases

### Removed

- Schema validation - API now accepts invalid data and reports errors server-side

# Changelog

All notable changes to `ameax-json-import-api` will be documented in this file.

## [Unreleased]

### Added
- **PDF Document Support**: New `DocumentPdf` model for attaching PDF files to receipts
  - Support for base64-encoded PDF content (`type: "base64"`)
  - Support for HTTPS URL references (`type: "url"`)
  - Added `setDocumentPdf()`, `setDocumentPdfFromBase64()`, `setDocumentPdfFromUrl()` methods to Receipt model
  - Automatic validation for PDF types and URL formats
- **Schema Updates**: Updated JSON schemas to latest version from ameax-json-schema project
  - Receipt schema now includes optional `document_pdf` field with conditional validation
  - Sale schema simplified amount validation (removed strict decimal constraints in schema, SDK maintains 2-decimal precision for financial standards)
- **Comprehensive Testing**: Added full test coverage for DocumentPdf functionality
  - DocumentPdf model validation tests
  - Receipt PDF attachment integration tests
  - Base64 and URL mode testing

### Changed
- **Sale Amount Validation**: Intentionally maintained stricter 2-decimal rounding in SDK despite relaxed schema constraints for financial data integrity
- **Documentation**: Updated README with PDF attachment examples and data structure documentation

### Technical Notes
- Schema-SDK divergence: Sale amount field maintains `round($amount, 2)` in SDK for monetary standards while schema allows flexible precision
- All changes maintain backward compatibility

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

# Changelog

All notable changes to `ameax-json-import-api` will be documented in this file.

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
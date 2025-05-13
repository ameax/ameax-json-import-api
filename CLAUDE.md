# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Information
This is a Laravel package for JSON import API functionality, built using the spatie/laravel-package-tools ameax-json-import-api.

## Commands
- **Test**: `composer test` (runs all tests with Pest)
- **Test Single**: `composer test tests/path/to/TestFile.php` or `composer test -- --filter=testName`
- **Static Analysis**: `composer analyse` (PHPStan level 6)
  - With additional checks enabled: checkMissingIterableValueType, checkUninitializedProperties, checkGenericClassInNonGenericObjectType
  - A PHPStan level 7 baseline is available for future stricter checking
- **Code Formatting**: `composer format` (Laravel Pint)
- **Test Coverage**: `composer test-coverage`

## Code Style Guidelines
- PHP 8.2+ required
- PSR-4 autoloading standards
- Follow Laravel conventions and naming
- Use strict type declarations for all parameters and return types (including mixed where appropriate)
- Always specify array types with their value types (e.g., `array<string, mixed>` instead of just `array`)
- Check that PHPStan reports no errors before committing changes
- Prefer dependency injection over facades in core code
- Handle exceptions gracefully, use custom exceptions when appropriate
- Use Laravel Pint defaults (based on PHP-CS-Fixer)
- Imports should be ordered alphabetically and grouped (PHP, Vendor, App)
- Use PHPDoc blocks for non-trivial methods
- Follow the package structure in src/ folder

## Future Improvements
- Enable remaining PHPStan checks:
  - checkImplicitMixed - To avoid implicit usage of mixed types
  - checkBenevolentUnionTypes - To enforce more precise union types
- Improve array type specifications by changing `array` to `array<string, mixed>` or more specific types
- Work toward passing PHPStan at level 7 to ensure maximum type safety
- Remove dependencies on mixed types where possible
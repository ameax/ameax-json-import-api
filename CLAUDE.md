# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Information
This is a Laravel package for JSON import API functionality, built using the spatie/laravel-package-tools ameax-json-import-api.

## Commands
- **Test**: `composer test` (runs all tests with Pest)
- **Test Single**: `composer test tests/path/to/TestFile.php` or `composer test -- --filter=testName`
- **Static Analysis**: `composer analyse` (PHPStan level 5)
- **Code Formatting**: `composer format` (Laravel Pint)
- **Test Coverage**: `composer test-coverage`

## Code Style Guidelines
- PHP 8.2+ required
- PSR-4 autoloading standards
- Follow Laravel conventions and naming
- Use strict type declarations for all parameters and return types (including mixed where appropriate)
- Prefer dependency injection over facades in core code
- Handle exceptions gracefully, use custom exceptions when appropriate
- Use Laravel Pint defaults (based on PHP-CS-Fixer)
- Imports should be ordered alphabetically and grouped (PHP, Vendor, App)
- Use PHPDoc blocks for non-trivial methods
- Follow the package structure in src/ folder
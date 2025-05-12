# Introduction to Ameax JSON Import API

This package provides a PHP interface for sending data to the Ameax API using their JSON schema format. It simplifies the process of creating, validating, and submitting data to Ameax.

## What is Ameax?

Ameax is an enterprise software system that provides various business management functionalities. The system accepts data imports through a JSON API, following specific schema definitions.

## About This Package

The Ameax JSON Import API package is designed to:

1. **Simplify Data Creation**: Helper methods to create properly structured data objects
2. **Validate JSON Data**: Validate your data against the official Ameax JSON schemas
3. **Handle API Communication**: Manage authentication, requests, and responses with the Ameax API
4. **Provide Comprehensive Error Handling**: Clear error messages for validation and API issues

## Key Features

- **Organization Data Support**: Create and send organization data to Ameax
- **JSON Schema Validation**: Validate your data before sending to prevent API errors
- **Laravel Integration**: Easy integration with Laravel applications
- **Comprehensive Documentation**: Detailed guides and API references
- **Modern PHP Support**: Built for PHP 8.2+ with type hints and modern PHP features

## Understanding Ameax JSON Schemas

The Ameax API uses JSON Schema to define the structure of data that can be sent to it. The schema defines:

- Required and optional fields
- Field types and formats
- Validation rules

This package includes schema definitions for organizations and provides an API that helps you create data that matches these schemas.

## Getting Started

To get started, refer to the [Installation Guide](installation.md) and then check out the [Organizations Guide](organizations.md) for creating and sending organization data.

For a complete list of available methods and classes, see the [API Reference](api-reference.md).
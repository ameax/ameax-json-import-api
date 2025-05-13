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

- **Organization & PrivatePerson Support**: Create and send organization and private person data to Ameax
- **Fluent Interface**: Chainable methods for cleaner, more readable code
- **Type Casting**: Automatic conversion of data types (strings to integers, booleans, etc.)
- **Semantic Versioning**: Clear version policy with backward compatibility guarantees
- **Comprehensive Documentation**: Detailed guides and API references
- **Modern PHP Support**: Built for PHP 8.2+ with strict type declarations and modern PHP features

## Understanding Ameax JSON Schemas

The Ameax API uses JSON Schema to define the structure of data that can be sent to it. The schema defines:

- Required and optional fields
- Field types and formats
- Validation rules

This package includes schema definitions for organizations and provides an API that helps you create data that matches these schemas.

## Getting Started

To get started, refer to the [Installation Guide](installation.md) and then check out the [Organizations Guide](organizations.md) for creating and sending organization data.

For a complete list of available methods and classes, see the [API Reference](api-reference.md).

For information about versioning and compatibility guarantees, see the [Versioning Guide](versioning.md).
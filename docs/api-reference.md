# API Reference

This document provides a reference for all classes and methods available in the Ameax JSON Import API package.

## Main Classes

### `Ameax\AmeaxJsonImportApi\AmeaxJsonImportApi`

The main client class for interacting with the Ameax API.

#### Constructor

```php
public function __construct(string $apiKey, string $host, ?string $schemasPath = null)
```

- `$apiKey`: Your Ameax API key for authentication
- `$host`: Your Ameax API host URL (e.g., 'https://your-database.ameax.de' or 'http://your-database.ameax.localhost')
- `$schemasPath`: Optional path to custom JSON schema files

#### Methods

**createOrganization**

```php
public function createOrganization(string $name, string $postalCode, string $locality, string $country): Organization
```

Creates a new organization with the required fields.

- `$name`: Organization name
- `$postalCode`: Postal/ZIP code
- `$locality`: City or town
- `$country`: ISO 3166-1 alpha-2 country code
- Returns: An Organization instance with the API client set

**organizationFromArray**

```php
public function organizationFromArray(array $data): Organization
```

Creates an organization from an existing array of data.

- `$data`: The organization data array
- Returns: An Organization instance with the API client set

### `Ameax\AmeaxJsonImportApi\Models\Organization`

Class for working with organization data and submitting it to the API.

#### Static Methods

**create**

```php
public static function create(
    string $name, 
    string $postalCode, 
    string $locality, 
    string $country
): self
```

Creates a new organization with required fields.

- `$name`: Organization name
- `$postalCode`: Postal/ZIP code
- `$locality`: City or town
- `$country`: ISO 3166-1 alpha-2 country code
- Returns: A new Organization instance

**fromArray**

```php
public static function fromArray(array $data): static
```

Creates a new organization from an array of data.

- `$data`: The organization data
- Returns: A new Organization instance

#### Instance Methods

**setApiClient**

```php
public function setApiClient(AmeaxJsonImportApi $apiClient): self
```

Sets the API client for this organization (required for submission).

- `$apiClient`: The API client
- Returns: The Organization instance for method chaining

**addContact**

```php
public function addContact(string $firstName, string $lastName, array $additionalData = []): self
```

Adds a contact to the organization.

- `$firstName`: Contact person's first name
- `$lastName`: Contact person's last name
- `$additionalData`: Optional additional contact data (email, phone, etc.)
- Returns: The Organization instance for method chaining

**setStreet**

```php
public function setStreet(string $street): self
```

Sets the street name in the address.

- `$street`: The street name
- Returns: The Organization instance for method chaining

**setHouseNumber**

```php
public function setHouseNumber(string $houseNumber): self
```

Sets the house number in the address.

- `$houseNumber`: The house number
- Returns: The Organization instance for method chaining

**setEmail**

```php
public function setEmail(string $email): self
```

Sets the organization's email address.

- `$email`: The email address
- Returns: The Organization instance for method chaining

**setPhone**

```php
public function setPhone(string $phone): self
```

Sets the organization's phone number.

- `$phone`: The phone number
- Returns: The Organization instance for method chaining

**setWebsite**

```php
public function setWebsite(string $website): self
```

Sets the organization's website.

- `$website`: The website URL
- Returns: The Organization instance for method chaining

**setVatId**

```php
public function setVatId(string $vatId): self
```

Sets the organization's VAT ID.

- `$vatId`: The VAT ID
- Returns: The Organization instance for method chaining

**setTaxId**

```php
public function setTaxId(string $taxId): self
```

Sets the organization's tax ID.

- `$taxId`: The tax ID
- Returns: The Organization instance for method chaining

**setCustomField**

```php
public function setCustomField(string $key, $value): self
```

Sets a custom field in the organization data.

- `$key`: The field key
- `$value`: The field value
- Returns: The Organization instance for method chaining

**toArray**

```php
public function toArray(): array
```

Converts the organization to an array.

- Returns: The organization data as an array

**submit**

```php
public function submit(): array
```

Submits the organization to the Ameax API.

- Returns: The API response as an array
- Throws: `\InvalidArgumentException` if no API client is set
- Throws: `ValidationException` if validation fails
- Throws: `\Exception` if the API request fails

### `Ameax\AmeaxJsonImportApi\Exceptions\ValidationException`

Exception thrown when JSON schema validation fails.

#### Methods

**getErrors**

```php
public function getErrors(): array
```

Gets the validation error messages.

- Returns: An array of validation error messages
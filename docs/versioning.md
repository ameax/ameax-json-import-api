# Versioning

This package follows [Semantic Versioning](https://semver.org/) (SemVer) to ensure a consistent and predictable versioning scheme.

## Version Format

Version numbers follow the `MAJOR.MINOR.PATCH` format:

- **MAJOR** version increases when incompatible API changes are made
- **MINOR** version increases when functionality is added in a backward-compatible manner
- **PATCH** version increases when backward-compatible bug fixes are implemented

## Composer Version Constraints

When requiring this package in your project, use the appropriate version constraint:

```json
{
    "require": {
        "ameax/ameax-json-import-api": "^1.0"
    }
}
```

The caret (`^`) operator means "compatible with version", allowing updates to any compatible future version.

## Version History

The package version history is maintained in the [CHANGELOG.md](../CHANGELOG.md) file in the root of the repository.

### Current Stable Version

The current stable version is **1.0.0**.

## Release Cycle

Releases are tagged in the GitHub repository using Git tags. Each release tag corresponds to a specific commit and is named with a "v" prefix followed by the version number (e.g., `v1.0.0`).

## Breaking Changes

Breaking changes are only introduced in MAJOR version increments. Examples of breaking changes include:

- Removing or renaming public methods/classes
- Changing method signatures (parameters, return types)
- Changing default behaviors
- Removing features
- Introducing strict type checking that wasn't previously enforced

## Development Versions

Development versions follow the pattern `1.x-dev` and are indicated in the composer.json with:

```json
{
    "extra": {
        "branch-alias": {
            "dev-main": "1.x-dev"
        }
    }
}
```

## Backward Compatibility Promise

For versions within the same MAJOR version number (e.g., 1.0.0 to 1.9.9):

- Existing public methods and classes will not be removed
- Method signatures will not change in incompatible ways
- Default behavior will not change in incompatible ways

If you need to rely on internal/private methods or behavior, note that these are not covered by the backward compatibility promise and may change between minor versions.
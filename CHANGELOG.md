# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Please note this changelog affects 
this package and not the Oxxa API.

## [2.8.1]

### Fixed

- Properly set lock value when retrieving a single domain.

## [2.8.0]

### Added

- Add task list and get endpoints.

## [2.7.0]

### Added

- Add additional error code when retrieving EPP for a locked domain.

## [2.6.2]

### Fixed

- Fixed date format for the `execution_at` property of the Domain model.
- Fixed date format for the `datebirth` and `idcarddate` property of the Identity model.

## [2.6.1]

### Fixed

- Not all TLD's will return the EPP code.

## [2.6.0]

### Added

- Add support for Laravel 11.

## [2.5.1]

### Changed

- Use `StatusCode` constants for the `checkStatus` method.

## [2.5.0]

### Added

- Add finished state to the register and transfer status endpoints.

### Fixed

- Properly pass the status in the transfer status endpoint.

## [2.4.1]

### Fixed

- Add missing price for additional domains when retrieving SSL products.

## [2.4.0]

### Added

- Allow additional parameters for the ssl products endpoint.

### Changed

- Moved pricing to a `Price` object.

## [2.3.0]

### Added

- Add `SSL_PRODUCT_LIST` command to the `Product` endpoint.

## [2.2.2]

### Fixed

- Set the proper parameter when requiring the trustee service for domains.

## [2.2.1]

### Fixed

- Use the proper status code for determining if the register or transfer action was successful.

## [2.2.0]

### Added

- Add status code to all endpoint responses to allow specific actions for certain status codes.

## [2.1.3]

### Fixed

- The `autoRenew` property of the `Domain` model is now handled as a boolean instead of the Y/N values.

## [2.1.2]

### Fixed

- Use the proper key for the handle when updating an identity.

## [2.1.1]

### Fixed

- The `converted` property of the `UserTldListRequest` should contain the valuta name instead of a boolean.

## [2.1.0]

### Added

- Add support for a custom base uri, useful for using a mock server for testing/implementing the API.

## [2.0.2]

### Fixed

- Update client typehint for better custom client support

## [2.0.1]

### Fixed

- Add missing `transferCode` and `dnssecDelete` properties to the `Domain` model.
- Use proper type for Dnssec properties.

## [2.0.0]

### Changed

- Switch from dynamic properties to real properties for easier usage.

## [1.0.0]

### Added

- Add initial release.
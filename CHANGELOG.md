# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Please note this changelog affects 
this package and not the Oxxa API.

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
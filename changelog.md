# Changelog
All notable changes to Neat System components will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.5.3] - 2024-08-15
### Changed
- Dropped support for php 7.0.
- Require neat/service >=0.3.1.
### Fixed
- Deprecation warnings for php 8+.

## [0.5.2] - 2020-05-18
### Added
- Modules class, path and paths methods.
### Fixed
- Modules->map() doesn't supports class/object methods.

## [0.5.1] - 2020-01-03
### Changed
- The module provider throws an ModuleNotFoundException in case the requested module doesn't exist.

## [0.5.0] - 2019-11-06
### Changed
- Kernel now requires a container instance in its constructor.
- Container self-injection configuration got moved to the boot method.

### Removed
- Kernel services method.

## [0.4.3] - 2019-11-05
### Fixed
- Register the container for self-injection.

## [0.4.2] - 2019-11-05
### Fixed
- Register the container for self-injection.

## [0.4.1] - 2019-10-31
### Added
- Modules implementation.
- Services interface.

## [0.4.0] - 2019-10-31
### Changed
- Fix release.

## [0.3.0] - pulled
### Changed
- Revert new kernel implementation using plugins and interfaces instead of callable handlers.
- Reintroduced Handler class.
- Reintroduced bootstrappers, handlers, terminators and failers methods in kernel.

## [0.2.0] - pulled
### Changed
- New kernel implementation using plugins and interfaces instead of callable handlers.
- Removed Handler class.
- Removed bootstrappers, handlers, terminators and failers methods from kernel.

## [0.1.2] - 2019-10-29
### Added
- Handler replace method.

## [0.1.1] - 2019-10-28
### Fixed
- Calling __invoke methods on handlers.
- Defining overridden constants on a Kernel subclass.

## [0.1.0] - 2019-10-28
### Added
- Handler implementation.
- Kernel implementation.

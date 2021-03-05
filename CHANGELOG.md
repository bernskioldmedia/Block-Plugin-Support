# Changelog

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.1] - 2021-03-05

### Fixed

-   In the section class, the attributes filter was not being applied correctly.

## [1.4.0] - 2021-02-18

### Added

-   Filters for dynamic blocks in `class Block`. This relies on the new `Blocks::$name` property being set for a block. With them you can extend attributes and block wrapper args. See more in the README.

## [1.3.2] - 2021-01-29

## Changed

-   Added support for background colors, gradients and text colors in dynamic blocks until Gutenberg get their act together.
-   Removed default background color because of conflict with gradient.

## [1.3.1] - 2021-01-29

## Fixed

-   Pass args down to is_content_shown
-   Add id="" support for sections.

## [1.3.0] - 2021-01-29

### Added

-   Support for disabling section wrappers.
-   Support for hiding content in a section before render. Useful if no results exist.

## [1.2.1] - 2020-12-29

### Fixed

-   Alignment classes wouldn't be properly applied on sections, this is fixed.
-   For sections, add the full alignment attribute as default.

## [1.2.0] - 2020-12-14

### Added

-   Section abstract class.

## [1.1.2] - 2020-09-26

### Changed

-   Switched Trait properties to functions with fallbacks.

## [1.1.1] - 2020-07-15

### Added

-   Class variable in our Has_Blocks trait to control the load order.

### Changed

-   Load order on init and plugins_loaded from default (10) to later (99) by default to make sure other plugins are loaded first.

## [1.1.0] - 2020-07-05

### Added

-   Better dependency management for blocks. You can now pass a specific list of dependencies for each block, or fallback on the editable global definition.

### Changed

-   The way textdomains are managed to the "proper" WP way.

## [1.0.2] - 2020-07-04

### Added

-   Customize the load order of the block assets.

### Changed

-   Load order of block assets are now 999 by default.

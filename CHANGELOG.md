# Changelog

## [1.6.0](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.6.0)

### Added

- Introduced a configurable The Courier Guy API URL setting in the plugin configuration, 
defaulting to https://api.portal.thecourierguy.co.za/ so merchants can override the endpoint for sandbox and
development environments.
- Validated the configurable API URL on save to require a well-formed HTTPS address.

### Changed

- Updated the module for compatibility with Magento 2.4.9 and PHP 8.5, including constructor property promotion,
standardised exception handling, and related code quality improvements.
 
### Improved

- Revalidate shipping rates before shipment creation and automatically select a valid fallback service 
level if the originally selected method is no longer available.

## [1.5.1](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.5.1)

### Added

- Added new courier service level codes: LPP, LFP, PRI, PRIR and LSP.

## [1.5.0](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.5.0)

### Changed

- Updated 'https://api.thecourierguy.co.za/v2/' to 'https://api.portal.thecourierguy.co.za/v2/'.
- Modernised codebase with PHP 8+ type declarations for improved type safety and performance.
- Updated property declarations to use typed properties throughout the codebase.
- Resolved deprecated class usage by replacing legacy classes with their modern equivalents.

## [1.4.0](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.4.0)

### Added

- Added the ability to set custom shipping labels and pricing to the checkout page.

## [1.3.2](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.3.2)

### Fixed

- Corrected an issue ensuring free shipping thresholds are calculated correctly against the cart subtotal before
  discounts are applied.

## [1.3.1](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.3.1)

### Fixed

- Corrected an issue ensuring shipping quotes accurately include tax from items.

## [1.3.0](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.3.0)

### Fixed

- Resolved an issue where specific product combinations caused inaccuracies in the packing algorithm, improving
  reliability and precision.

### Added

- Introduced a toggleable setting for enabling or disabling shipping insurance, providing users with greater control
  over their preferences.

## [1.2.3](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.2.3)

### Fixed

- Resolved issue where product attribute dimensions (length, width, height) were being calculated incorrectly, ensuring
  accurate shipping rate calculations.
- Addressed compatibility issue with certain Gift Card modules, overriding the discount after selecting The Courier Guy
  as a shipping method.

## [1.2.2](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.2.2)

### Fixed

- Remove default declared value field to disable forced shipping insurance.
- Add Order Increment ID as customer_reference for Waybill.

## [1.2.1](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.2.1)

### Fixed

- Virtual products no longer displayed on Waybill.
- Flat rate and free shipping work together as expected.

## [1.2.0](https://github.com/The-Courier-Guy/TCG_Magento_2/releases/tag/v1.2.0)

### Added

- Return order functionality.
- Bug fixes and improvements.

### Changed

- Switched TCG account auth to tokenised api key.

### Fixed

- Fixed an issue with auto-submit shipment.
- Fixed an issue with Amasty Checkout.

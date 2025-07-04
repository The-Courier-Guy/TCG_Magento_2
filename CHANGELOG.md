# Changelog

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

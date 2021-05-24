# Changelog
- All notable changes to this project will be documented in this file.
- See [changelog structure](https://keepachangelog.com/en/0.3.0/) for more information of how to write perfect changelog.

## Release note
- Make sure what version is required for the client. Is it production or testing
- Make sure why developing, set DISABLE_CACHE to true in order for dependency injection loaded containers would change.
  Otherwise, they are in immutable state.
- When developing, set DEV_MODE to true in order for test BASE_URL and Register/Login
- When providing the zip , make sure there are no .git or var folder
- Make sure to create git tags when adding new version. Use git tag -a vx.x.x commit-hash -m 'release of vx.x.x'
- Install vendors using composer install --no-dev --optimize-autoloader


## [2.0.0] - 2018-09-24

### Changed
- enabled production version by default
- cached dependency injection container

## [2.1.0] - 2018-10-01

### Changed
- added DEV_MODE setting to keep test BASE_URL and Register/Login
- removed relog function from production mode
- fixed cancel and success callbacks issue


## [2.1.1] - 2018-11-07

### Changed
- added setting in BO to chose if display ViaBill logo in checkout payment selection step
- fixed Go To MyViaBill button error, not it not display field if it's empty
- now Go To MyViaBill button error will be shown ass warning to stop preventing settings changes

## [2.1.2] - 2018-11-14

### Changed
- fixed capture/refund issue on prices with comma as thousands separator
- added missing Da/No module translations

## [2.1.3] - 2019-01-07

### Changed
- added terms and conditions for US
- added price tag for US
- payment service now accepting US locale

### Changed
## [2.1.4] - 2019-03-06

### Changed
- added different payment logo for US currency

## [2.1.5] - 2019-07-01

### Changed
- removed different payment logo for US currency
- fixed ViaBill transaction not appearing on customer account issue
- added auto full capture functionality when the status of the order is changed to "Payment completed by ViaBill"
- disallowed merchants to change order status if order status is "Payment pending by ViaBill"

### Changed
## [2.1.6] - 2019-11-04
- version update to equalize versions with 1.7 module

### Changed
## [2.1.7] - 2019-11-04
- improved API request exceptions cachin
- refactored Terms&Conditions link. Now link is made from country code taken from API locales instead of hardcoded.  
    In this way no extra work will be needed in future for T&C link.g functionality to prevent page breaks when request fails

### Changed
## [2.1.8] - 2020-01-21
- added auto-capture and order statuses multiselect for auto-capture settings in module BO setting tab.
- changed order status hook logic to capture orders with auto-capture multiselect setting statuses in module BO instead
    of hardcoded "Payment completed by ViaBill" status.
- refactored Terms&Conditions link. Now link is made from country code taken from API locales instead of hardcoded.  
    In this way no extra work will be needed in future for T&C link.
    
### Changed
## [2.1.9] - 2020-01-31
- recreated auto-capture when order status is set to "Payment completed by ViaBill".
- added data-country-code tag in priceTag.
- Added Spanish translations

### Changed
## [2.1.10] - 2020-02-21
- added functionality that changes order status to "Payment cancelled by ViaBill" when "cancelled" or "rejected" callback is received. Order status change by hand is still not allowed when status is pending.

### Changed
## [2.1.11] - 2020-03-06
- fixed order status revert issue when ViaBill callback is setting order to accepted state and auto-capture is enabled on accepted state.

### Changed
## [2.1.12] - 2020-08-05
- changed capture amount from float to string when sending capture request via api.

### Changed
## [2.1.13] - 2020-12-17
- added cart duplication functionality to duplicate customers cart when canceling the order.

### Changed
## [2.1.14] - 2020-12-23
- fixed viabill_pending_order_cart database table install issue
- added viabill_pending_order_cart table to uninstall functionality
- Viabill order status change validation now works only in back-office

### Changed
## [2.1.15] - 2021-04-19
- Bug fixes & code refactoring
- Improved logging capabilities
- Built-in contact form for technical support
- Troubleshooting section
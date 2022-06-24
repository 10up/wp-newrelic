# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/).

## [Unreleased] - TBD

## [1.3.1] - 2020-01-31
### Added
- Check for New Relic functions before attempting to use them (props [@msaggiorato](https://github.com/msaggiorato) via [#31](https://github.com/10up/wp-newrelic/pull/31))
- Plugin banner and icon images (props [@sncampbell](https://github.com/sncampbell) via [#40](https://github.com/10up/wp-newrelic/pull/40))
- Documentation on PHP version conflict (props [@jeffpaul](https://github.com/jeffpaul) via [#41](https://github.com/10up/wp-newrelic/pull/41))

### Fixed
- Sitemap check failures where global `wp_query` was undefined (props [@msaggiorato](https://github.com/msaggiorato) via [#30](https://github.com/10up/wp-newrelic/pull/30))

## [1.3] - 2018-08-30
### Added
- Support for Yoast SEO Sitemaps (props [@allan23](https://github.com/allan23) via [#26](https://github.com/10up/wp-newrelic/pull/26))
- Issue with Beaver Builder (props [@oscarssanchez](https://github.com/oscarssanchez) via [#27](https://github.com/10up/wp-newrelic/pull/27))

## [1.2] - 2018-05-03
### Added
- Support for REST API requests (props [@Rahe](https://github.com/Rahe) via [#21](https://github.com/10up/wp-newrelic/pull/21))
- composer.json (props [@herewithme](https://github.com/herewithme) via [#22](https://github.com/10up/wp-newrelic/pull/22))

### Changed
- Better naming for transactions (props [@eugene-manuilov](https://github.com/eugene-manuilov) via [#19](https://github.com/10up/wp-newrelic/pull/19))

### Fixed
- PHP warning (props [@ninnypants](https://github.com/ninnypants) via [#18](https://github.com/10up/wp-newrelic/pull/18))

## [1.1] - 2017-04-25
### Added
- Transaction grouping (props [@nicholasio](https://github.com/nicholasio) via [#6](https://github.com/10up/wp-newrelic/pull/6))
- Constant to allow disabling of installation notice (props [@tott](https://github.com/tott) via [#15](https://github.com/10up/wp-newrelic/pull/15))

### Changed
- Disabling NREUM on AMP pages (props [@goldenapples](https://github.com/goldenapples) via [#11](https://github.com/10up/wp-newrelic/pull/11))

### Fixed
- PHP warning (props [@allan23](https://github.com/allan23) via [#13](https://github.com/10up/wp-newrelic/pull/13))

## [1.0] - 2017-01-09
### Added
- First release of WP New Relic plugin 🎉

[Unreleased]: https://github.com/10up/wp-newrelic/compare/trunk...develop
[1.3.1]: https://github.com/10up/wp-newrelic/compare/1.3...1.3.1
[1.3]: https://github.com/10up/wp-newrelic/compare/d70cf93...1.3
[1.2]: https://github.com/10up/wp-newrelic/compare/49f4e79...d70cf93
[1.1]: https://github.com/10up/wp-newrelic/compare/9ec2b8d...49f4e79
[1.0]: https://github.com/10up/wp-newrelic/tree/9ec2b8d5c9e72504052a98cbb76d2e4b2e1b2b29

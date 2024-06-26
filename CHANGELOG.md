# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/).

## [Unreleased] - TBD

## [1.3.3] - 2024-06-26
**Note this release bumps the PHP minimum from 7.3.11 to 8.0 and the WordPress minimum from 4.0 to 6.3.**

### Added
- Minimum PHP version check for 7.3.11 (props [@bmarshall511](https://github.com/bmarshall511), [@Sidsector9](https://github.com/Sidsector9) via [#60](https://github.com/10up/wp-newrelic/pull/60)).

### Changed
- [Support Level](https://github.com/10up/wp-newrelic?tab=readme-ov-file#support-level) downgraded from `Active` to `Stable` (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter), [@felipeelia](https://github.com/felipeelia), [@oscarssanchezz](https://github.com/oscarssanchezz) via [#65](https://github.com/10up/wp-newrelic/pull/65)).
- Bump WordPress "tested up to" version 6.5 (props [@zamanq](https://github.com/zamanq), [@jeffpaul](https://github.com/jeffpaul), [@oscarssanchezz](https://github.com/oscarssanchezz) via [#58](https://github.com/10up/wp-newrelic/pull/58), [#66](https://github.com/10up/wp-newrelic/pull/66)).

### Fixed
- Improve compatibility with PHP 8.1 by replacing the deprecated `FILTER_SANITIZE_STRING` (props [@burhandodhy](https://github.com/burhandodhy), [@jeffpaul](https://github.com/jeffpaul), [@felipeelia](https://github.com/felipeelia), [Sven](mailto:venms@gmail.com) via [#68](https://github.com/10up/wp-newrelic/pull/68)).

### Developer
- Create `dependency-review.yml` GitHub Action (props [@jeffpaul](https://github.com/jeffpaul) via [#54](https://github.com/10up/wp-newrelic/pull/54)).
- Replaced [lee-dohm/no-response](https://github.com/lee-dohm/no-response) with [actions/stale](https://github.com/actions/stale) to help with closing no-response/stale issues (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#64](https://github.com/10up/wp-newrelic/pull/64)).

## [1.3.2] - 2022-06-28
### Changed
- Bump WordPress version "tested up to" 6.0 (props [@lukecav](https://github.com/lukecav), [@burhandodhy](https://github.com/burhandodhy), [@jeffpaul](https://github.com/jeffpaul) via [#45](https://github.com/10up/wp-newrelic/pull/45), [#49](https://github.com/10up/wp-newrelic/pull/49)).

## [1.3.1] - 2020-01-31
### Added
- Check for New Relic functions before attempting to use them (props [@msaggiorato](https://github.com/msaggiorato) via [#31](https://github.com/10up/wp-newrelic/pull/31)).
- Plugin banner and icon images (props [@sncampbell](https://github.com/sncampbell) via [#40](https://github.com/10up/wp-newrelic/pull/40)).
- Documentation on PHP version conflict (props [@jeffpaul](https://github.com/jeffpaul) via [#41](https://github.com/10up/wp-newrelic/pull/41)).
- GitHub Actions for plugin and asset deploys to WordPress.org (props [@jeffpaul](https://github.com/jeffpaul) via [#34](https://github.com/10up/wp-newrelic/pull/34)).

### Changed
- Bump WordPress version "tested up to" 5.3 (props [@jeffpaul](https://github.com/jeffpaul) via [#35](https://github.com/10up/wp-newrelic/pull/35)).

### Fixed
- Sitemap check failures where global `wp_query` was undefined (props [@msaggiorato](https://github.com/msaggiorato) via [#30](https://github.com/10up/wp-newrelic/pull/30)).

## [1.3] - 2018-08-30
### Added
- Support for Yoast SEO Sitemaps (props [@allan23](https://github.com/allan23) via [#26](https://github.com/10up/wp-newrelic/pull/26)).
- Issue with Beaver Builder (props [@oscarssanchez](https://github.com/oscarssanchez) via [#27](https://github.com/10up/wp-newrelic/pull/27)).

## [1.2] - 2018-05-03
### Added
- Support for REST API requests (props [@Rahe](https://github.com/Rahe) via [#21](https://github.com/10up/wp-newrelic/pull/21)).
- composer.json (props [@herewithme](https://github.com/herewithme) via [#22](https://github.com/10up/wp-newrelic/pull/22)).

### Changed
- Better naming for transactions (props [@eugene-manuilov](https://github.com/eugene-manuilov) via [#19](https://github.com/10up/wp-newrelic/pull/19)).

### Fixed
- PHP warning (props [@ninnypants](https://github.com/ninnypants) via [#18](https://github.com/10up/wp-newrelic/pull/18)).

## [1.1] - 2017-04-25
### Added
- Transaction grouping (props [@nicholasio](https://github.com/nicholasio) via [#6](https://github.com/10up/wp-newrelic/pull/6)).
- Constant to allow disabling of installation notice (props [@tott](https://github.com/tott) via [#15](https://github.com/10up/wp-newrelic/pull/15)).

### Changed
- Disabling NREUM on AMP pages (props [@goldenapples](https://github.com/goldenapples) via [#11](https://github.com/10up/wp-newrelic/pull/11)).

### Fixed
- PHP warning (props [@allan23](https://github.com/allan23) via [#13](https://github.com/10up/wp-newrelic/pull/13)).

## [1.0] - 2017-01-09
### Added
- First release of WP New Relic plugin 🎉

[Unreleased]: https://github.com/10up/wp-newrelic/compare/trunk...develop
[1.3.3]: https://github.com/10up/wp-newrelic/compare/1.3.2...1.3.3
[1.3.2]: https://github.com/10up/wp-newrelic/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/10up/wp-newrelic/compare/1.3...1.3.1
[1.3]: https://github.com/10up/wp-newrelic/compare/d70cf93...1.3
[1.2]: https://github.com/10up/wp-newrelic/compare/49f4e79...d70cf93
[1.1]: https://github.com/10up/wp-newrelic/compare/9ec2b8d...49f4e79
[1.0]: https://github.com/10up/wp-newrelic/tree/9ec2b8d5c9e72504052a98cbb76d2e4b2e1b2b29

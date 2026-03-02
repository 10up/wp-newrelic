# WP New Relic

![WP New Relic](https://github.com/10up/wp-newrelic/blob/develop/.wordpress-org/banner-1544x500.png)

[![Support Level](https://img.shields.io/badge/support-archived-red.svg)](#support-level) ![Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/wp-newrelic?label=Requires%20PHP) ![Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/wp-newrelic?label=Requires%20WordPress) [![Release Version](https://img.shields.io/github/release/10up/wp-newrelic.svg)](https://github.com/10up/wp-newrelic/releases/latest) ![WordPress tested up to version](https://img.shields.io/wordpress/plugin/tested/wp-newrelic?label=WordPress) [![GPL-2.0-or-later License](https://img.shields.io/github/license/10up/wp-newrelic.svg)](https://github.com/10up/wp-newrelic/blob/develop/LICENSE.md) [![Dependency Review](https://github.com/10up/wp-newrelic/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/10up/wp-newrelic/actions/workflows/dependency-review.yml) [![E2E Tests](https://github.com/10up/wp-newrelic/actions/workflows/cypress.yml/badge.svg)](https://github.com/10up/wp-newrelic/actions/workflows/cypress.yml) [![WordPress Playground Demo](https://img.shields.io/wordpress/plugin/v/wp-newrelic?logo=wordpress&logoColor=FFFFFF&label=Playground%20Demo&labelColor=3858E9&color=3858E9)](https://playground.wordpress.net/?plugin=wp-newrelic)

> New Relic APM reports for WordPress.

> [!CAUTION]
> As of 2 March 2026, this project is archived and no longer being actively maintained.

## Overview

WP New Relic (WPNR) is designed to be used with the [New Relic APM](https://newrelic.com/application-monitoring), and uses the [New Relic PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api) to augment existing metrics with valuable WordPress details such as templates, users, request type, and [Transaction](https://docs.newrelic.com/docs/apm/transactions) names. This plugin is tested with New Relic's PHP Agent version 10.19.0.9. Data collected by this plugin can be queried in [New Relic's Insights](https://newrelic.com/insights/) product, using [New Relic Query Language (NRQL)](https://docs.newrelic.com/docs/insights/new-relic-insights/using-new-relic-query-language/nrql-reference).

New Relic is a trademark of New Relic, Inc.

## Administrative settings

After activating the plugin, You will see a new option named __New Relic__ under the Tools menu in your WordPress Dashboard. If you are running WordPress in Network Mode, the settings page will be found in your Network Dashboard.

![wp-nr-settings](https://cloud.githubusercontent.com/assets/2941333/21731687/906addd0-d47b-11e6-9f58-e71c17425906.png)

### Capture URL Parameters

If the __Capture URLs__ setting is enabled, the plugin will capture URL parameters for displaying in Transaction traces. As an example, turning on this feature will store a URL like http://example.com/?p=1234, while leaving it off will result in the URL being stored as http://example.com/. This feature can be useful for debugging or providing granular data if required. In certain cases, however, it can cause confusion by creating a "false positive" appearance of multiple URLs (e.g. UTM codes or tracking info from social media).

## Basic Config

By default the plugin will setup 3 New Relic configuration parameters:
- [newrelic.appname](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-configuration#inivar-appname)
- [newrelic.capture_params](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-configuration#inivar-capture_params)
- [newrelic.framework](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-configuration#inivar-framework) (default value is 'wordpress')

__appname__ and __capture_params__ config can be overridden using the [__wp_nr_config__](https://github.com/10up/wp-newrelic/blob/9ec2b8d5c9e72504052a98cbb76d2e4b2e1b2b29/classes/class-wp-nr-apm.php#L36) filter. The [__wp_nr_setup_config__](https://github.com/10up/wp-newrelic/blob/9ec2b8d5c9e72504052a98cbb76d2e4b2e1b2b29/classes/class-wp-nr-apm.php#L51) action hook can also be used to setup any extra config.

## New Relic Custom Attributes

Certain useful custom attributes (you can think of these as 'post meta for New Relic') will be set for each event and can provide additional information related to your WordPress installation. You can query events and filter them using these attributes in New Relic Insights. See the [NRQL reference](https://docs.newrelic.com/docs/insights/new-relic-insights/using-new-relic-query-language/nrql-reference) for more details on how to query events.

### User

The user attribute is set using [newrelic_set_user_attributes](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-set-user-attributes). If the user is logged in, the user ID will be set as the user attribute and if not the user attribute will be set to `not-logged-in`.
Ex: In New Relic Insights you can query Transactions for non-logged in users as
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND user = 'not-logged-in'
```

### Post ID

For single posts, the post ID will be set via the `post_id` custom attribute.

Ex: Get all Transactions for a post with ID 190.
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND post_id = '190'
```

You can also perform more complex queries, such as counting the number of views for a post. This can be helpful for determining top content on your site.
```
SELECT count(*) FROM Transaction WHERE appName = '{appName}' AND post_id = '190'
```

### Template

For each request, the Template being used is set as the `template` custom attribute.

Ex: You can query all Transactions for a particular template.
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND template = '{Absolute Template Path}'
```

### Theme

The current theme is set as the `theme` custom attribute.

Ex: Query all Transactions running the "Twenty Fifteen" theme.
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND theme = 'Twenty Fifteen'
```

### Request type

There can be 3 type of requests: __web__, __ajax__ and __cli__. The current request type is set as the `request_type` custom attribute.
The request type can be overridden using the __wp_nr_request_type__ filter.

Ex: Get all Transactions for the "ajax" request type
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND request_type = 'ajax'
```

### Transaction Name

The Transaction name is set based on the main WP_Query parameters using [newrelic_name_transaction](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-name-wt).
Possible values are Default Home Page, Front Page, Blog Page, Network Dashboard, Dashboard, Single - {post_type}, Page - {pagename}, Date Archive, Search Page, Feed, Archive - {post_type}, Category - {cat_name}, Tag - {tag_name}, Tax - {taxonomy} - {term}

### Custom Error Logging

Using the __wp_nr_log_errors__ function, any plugin/theme can log errors/notices to New Relic for the current Transaction.
Note: This function can be called more than once, but only the last call will log the error to New Relic. This is a known limitation of the [PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-notice-error). As a reminder, since the PHP Agent runs only when PHP does, any cached requests will not appear in your error logs.

```
wp_nr_log_errors( 'Error message', Exception $exception );
```

### Runtime of async tasks

This plugin also tracks the runtime of [gearman](https://github.com/10up/WP-Gears) async tasks. A Gearman async task run for a particular hook and its runtime can be tracked using the “wp_async_task-{hook}” custom attribute and queried in New Relic Insights.

## Screenshots

![wp-nr-transactions](https://cloud.githubusercontent.com/assets/2941333/20933334/bccaf1bc-bbfd-11e6-92a5-6da6dff31cf0.png)
(Transactions in New Relic APM)

![wp-nr-single-post-query](https://cloud.githubusercontent.com/assets/2941333/20933383/e032337c-bbfd-11e6-8ee6-87b1783cb1ad.png)
(New Relic Insights query for a single post)

![wp-nr-total-post-view-count](https://cloud.githubusercontent.com/assets/2941333/20933411/f2be3bd0-bbfd-11e6-847a-08f8a838d968.png)
(Total post view counts for a single post using post_id custom parameter)

![wp-nr-databaseduration-query](https://cloud.githubusercontent.com/assets/2941333/20933427/ffb5652a-bbfd-11e6-97fa-ca68d66c579d.png)
(Get Template used and Transactions whose database duration is more than 0.1)

## WordPress Playground Integration

This plugin includes integration with [WordPress Playground](https://wordpress.github.io/wordpress-playground/), allowing you to test and demonstrate the plugin in a browser-based WordPress environment.

### Try it in Playground

You can launch WordPress Playground with this plugin pre-installed using one of these methods:

**Direct URL:**
```
https://playground.wordpress.net/?plugin=wp-newrelic
```

**Using Blueprint:**
A blueprint file is included at `.github/blueprints/blueprint.json` that automatically installs and activates the plugin. You can use this blueprint URL:
```
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/10up/wp-newrelic/develop/.github/blueprints/blueprint.json
```

### Important Notes

- **New Relic Extension Not Available**: WordPress Playground runs PHP in a browser environment using WebAssembly. The New Relic PHP extension is not available in this environment, so the plugin will display an informational notice explaining this limitation.
- **For Testing Only**: Playground is ideal for testing plugin functionality, exploring settings, and demonstrating features. For production use, install the plugin on a server with the New Relic PHP agent installed.
- **Environment Detection**: The plugin automatically detects when running in WordPress Playground and provides appropriate messaging. You can disable the Playground notice by defining `WP_NR_DISABLE_PLAYGROUND_NOTICE` as `true`.

## Known Issues/Caveats

### PHP version

PHP version 7.3.3 is known to cause issues with this plugin, updating to 7.3.11 or greater appears to resolve these issues.  For more details, see [issue#39](https://github.com/10up/wp-newrelic/issues/39).

## Frequently Asked Questions

### Have an issue to report?

If you identify any errors or have an idea for improving the plugin, please open an [issue](https://github.com/10up/wp-newrelic/issues?stage=open). We're excited to see what the community thinks of this project, and we would love your input!

### Where do I report security bugs found in this plugin?

Please report security bugs found in the source code of the New Relic Reporting for WordPress plugin through the [Patchstack Vulnerability Disclosure  Program](https://patchstack.com/database/vdp/d1d1019e-23d3-4908-b1be-6caac89d4eb6).  The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.

## Support Level

**Archived:** This project is no longer maintained by 10up. We are no longer responding to Issues or Pull Requests unless they relate to security concerns. We encourage interested developers to fork this project and make it their own!

## Like what you see?

<a href="http://10up.com/contact/"><img src="https://github.com/10up/.github/blob/trunk/profile/10up-github-banner.jpg" width="850" alt="Work with the 10up WordPress Practice at Fueled"></a>

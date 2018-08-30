=== New Relic Reporting for WordPress ===
Contributors: rittesh.patel, tott, 10up, oscarssanchez
Tags: New Relic, New Relic Reporting, New Relic APM Reporting, APM Reporting
Requires at least: 4.0
Tested up to: 4.9.8
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

New Relic APM reports for WordPress.

== Description ==

WP New Relic (WPNR) is designed to use with [New Relic APM](https://newrelic.com/application-monitoring) which uses [New Relic PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api) to properly augment existing metrics with valuable WordPress details such as templates, users, request type, transaction names etc. This plugin is tested with PHP Agent 6.7.0.174.

New Relic is a trademark of New Relic, Inc.

= Admin Settings =

After activating the plugin, You will see a new option named __New Relic__ under the Tools menu in your WordPress Dashboard. If you are running WordPress in Network Mode, the settings page will be found in your Network Dashboard.

Capture URL Parameters: If Capture URLs setting is enabled, it will capture URL parameters for displaying in transaction traces.

= Basic Config =

By default plugin will setup 3 configs.
- newrelic.appname
- newrelic.capture_params
- newrelic.framework (value is wordpress)

appname and capture_params config can be override using __wp_nr_config filter__. __wp_nr_setup_config__ action hook can also be used to setup any extra config.

= New Relic Custom Attributes =

Certain useful custom attrribute (just like WordPress post meta) will be set for each event which can provide additional information using key-value pair. You can query events and filter them using these attributes in New Relic Insights. See [NRQL reference](https://docs.newrelic.com/docs/insights/new-relic-insights/using-new-relic-query-language/nrql-reference) for more details on how to query events.

1. __User__

    User attribute is getting set using [newrelic_set_user_attributes](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-set-user-attributes). If user is logged in, user ID will be set to user attribute and if not it will be set to __not-logged-in__.
    Ex: In New Relic Insights you can query transactions for non-logged in users as
    SELECT * FROM Transaction WHERE appName = '{appName}' AND user = 'not-logged-in'

2. __Post ID__

    For single post, post ID will be set via __post_id__ custom parameter in transaction.

    Ex: Get all transactions for a post ID 190.

    SELECT * FROM Transaction WHERE appName = '{appName}' AND post_id = '190'

    One can also query for post view count for post 190.

    SELECT count(*) FROM Transaction WHERE appName = '{appName}' AND post_id = '190'

3. __Template__

    For each request, template being used getting set as __template__ custom parameter in transaction.

    Ex: You can query all transactions for a particular template as following.

    SELECT * FROM Transaction WHERE appName = '{appName}' AND template = '{Absolute Template Path}'

4. __Theme__

    Current theme is getting set as __theme__ custom parameter in transaction.

    Ex: Query all transactions for "Twenty Fifteen" theme.

    SELECT * FROM Transaction WHERE appName = '{appName}' AND theme = 'Twenty Fifteen'

5. __Request type__

    There can be 3 type of requests namely web, ajax and cli. Current request type getting set as __request_type__ custom parameter in transaction.
    Request type can be override using wp_nr_request_type filter.

    Ex: Get all transaction for "ajax" request type

    SELECT * FROM Transaction WHERE appName = '{appName}' AND request_type = 'ajax'

6. __Transaction Name__

    Transaction name is getting set as per the main WP_Query parameters using [newrelic_name_transaction](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-name-wt).
    Possible values are Default Home Page, Front Page, Blog Page, Network Dashboard, Dashboard, Single - {post_type}, Page - {pagename}, Date Archive, Search Page, Feed, Archive - {post_type}, Category - {cat_name}, Tag - {tag_name}, Tax - {taxonomy} - {term}


7. __Custom Error Logging__

    Using __wp_nr_log_errors__ function, any plugin/theme can log errors/notices to New Relic for current transaction.
    Note: This function can be used more than once but only last use will be considered to log the error to New Relic. It’s limitation of [PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-notice-error).

    __wp_nr_log_errors( 'Error message', Exception $exception );__

8. __Runtime of async tasks__

    This plugin also tracks runtime of [gearman](https://github.com/10up/WP-Gears) async tasks. Gearman async task run for a particular hook and it’s runtime can be track using “wp_async_task-{hook}” custom attribute in New Relic Insights.

= Issues =

If you identify any errors or have an idea for improving the plugin, please open an [issue](https://github.com/10up/wp-newrelic/issues?stage=open). We're excited to see what the community thinks of this project, and we would love your input!

== Installation ==
1. First, you will need to [install and configure](https://docs.newrelic.com/docs/agents/php-agent/getting-started/new-relic-php) New Relic PHP agent on server.
2. Activate the plugin in WordPress.
3. Enjoy!

== Changelog ==

= 1.3 =
* Bug fixes with Beaver Builder (props oscarssanchez)
* Support for Yoast SEO Sitemaps.


= 1.2 =
* Add support for REST API requests (props Rahe)
* PHP warning fix (props ninnypants)
* Better naming for transactions (props eugene-manuilov)
* Add composer.json (props herewithme)

= 1.1 =
* Transaction grouping. (Props nicholasio)
* Disabling NREUM on AMP pages. (Props goldenapples)
* PHP warning fix.
* Constant to allow disabling of installation notice. (Props tott)

= 1.0 =
* First release of WP New Relic plugin.

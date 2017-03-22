# WP New Relic

WP New Relic (WPNR) is designed to be used with the [New Relic APM](https://newrelic.com/application-monitoring), and uses the [New Relic PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api) to augment existing metrics with valuable WordPress details such as templates, users, request type, and [Transaction](https://docs.newrelic.com/docs/apm/transactions) names. This plugin is tested with New Relic's PHP Agent version 6.7.0.174. Data collected by this plugin can be queried in [New Relic's Insights](https://newrelic.com/insights/) product, using [New Relic Query Language (NRQL)](https://docs.newrelic.com/docs/insights/new-relic-insights/using-new-relic-query-language/nrql-reference).

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

## Development

The WP New Relic plugin is developed and maintained by 10up, Inc.
<p align="center">
<a href="http://10up.com/contact/"><img src="https://10updotcom-wpengine.s3.amazonaws.com/uploads/2016/10/10up-Github-Banner.png" width="850"></a>
</p>

### License

The WP New Relic plugin is released under the [GNU Public License v2](http://www.gnu.org/licenses/gpl-2.0.html) or later.

### Issues

If you identify any errors or have an idea for improving the plugin, please open an [issue](https://github.com/10up/wp-newrelic/issues?stage=open). We're excited to see what the community thinks of this project, and we would love your input!

# WP New Relic

WP New Relic (WPNR) is designed to use with [New Relic APM](https://newrelic.com/application-monitoring) which uses [New Relic PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api) to properly augment existing metrics with valuable WordPress details such as templates, users, request type, transaction names etc. This plugin is tested with PHP Agent 6.7.0.174.

New Relic is a trademark of New Relic, Inc.

## Admins settings

Admin settings can be browse after activating plugin. You should see a new option named __New Relic__ under Tools menu. In case of WPMU, it could be found in network dashboard.

![wp-nr-settings](https://cloud.githubusercontent.com/assets/2941333/21731687/906addd0-d47b-11e6-9f58-e71c17425906.png)

### Capture URL Parameters

If Capture URLs setting is enabled, it will capture URL parameters for displaying in transaction traces.

### Disable for AMP

If Disable for AMP setting is enabled, it will disable New Relic for AMP endpoints.

## Basic Config

By default plugin will setup 3 configs.
- newrelic.appname
- newrelic.capture_params
- newrelic.framework (value is wordpress)

appname and capture_params config can be override using __wp_nr_config filter__. __wp_nr_setup_config__ action hook can also be used to setup any extra config.

## New Relic Custom Attributes

Certain useful custom attrribute (just like WordPress post meta) will be set for each event which can provide additional information using key-value pair. You can query events and filter them using these attributes in New Relic Insights. See [NRQL reference](https://docs.newrelic.com/docs/insights/new-relic-insights/using-new-relic-query-language/nrql-reference) for more details on how to query events.

### User

User attribute is getting set using [newrelic_set_user_attributes](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-set-user-attributes). If user is logged in, user ID will be set to user attribute and if not it will be set to`not-logged-in`.  
Ex: In New Relic Insights you can query transactions for non-logged in users as
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND user = 'not-logged-in'
```

### Post ID
For single post, post ID will be set via `post_id` custom parameter in transaction.
  
Ex: Get all transactions for a post ID 190.
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND post_id = '190'
```

One can also query for post view count for post 190.
```
SELECT count(*) FROM Transaction WHERE appName = '{appName}' AND post_id = '190'
```

### Template

For each request, template being used getting set as `template` custom parameter in transaction.

Ex: You can query all transactions for a particular template as following.
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND template = '{Absolute Template Path}'
```

### Theme

Current theme is getting set as `theme` custom parameter in transaction.

Ex: Query all transactions for "Twenty Fifteen" theme.
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND theme = 'Twenty Fifteen'
```

### Request type

There can be 3 type of requests namely web, ajax and cli. Current request type getting set as `request_type` custom parameter in transaction.
Request type can be override using wp_nr_request_type filter.

Ex: Get all transaction for "ajax" request type
```
SELECT * FROM Transaction WHERE appName = '{appName}' AND request_type = 'ajax'
```

### Transaction Name

Transaction name is getting set as per the main WP_Query parameters using [newrelic_name_transaction](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-name-wt). 
Possible values are Default Home Page, Front Page, Blog Page, Network Dashboard, Dashboard, Single - {post_type}, Page - {pagename}, Date Archive, Search Page, Feed, Archive - {post_type}, Category - {cat_name}, Tag - {tag_name}, Tax - {taxonomy} - {term}

### Custom Error Logging

Using __wp_nr_log_errors__ function, any plugin/theme can log errors/notices to New Relic for current transaction. 
Note: This function can be used more than once but only last use will be considered to log the error to New Relic. It’s limitation of [PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-notice-error).

```
wp_nr_log_errors( 'Error message', Exception $exception );
```

### Runtime of async tasks

This plugin also tracks runtime of [gearman](https://github.com/10up/WP-Gears) async tasks. Gearman async task run for a particular hook and it’s runtime can be track using “wp_async_task-{hook}” custom attribute in New Relic Insights.

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

### Issues

If you identify any errors or have an idea for improving the plugin, please open an [issue](https://github.com/10up/wp-newrelic/issues?stage=open). We're excited to see what the community thinks of this project, and we would love your input!

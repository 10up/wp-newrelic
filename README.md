# WP New Relic

WP New Relic (WPNR) uses [New Relic PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api) to properly augment existing metrics with valuable WordPress details such as templates, users, request type, transaction names etc. This plugin is tested with PHP Agent 6.7.0.174.

## Admins settings

Admin settings can be browse after activating plugin. You should see a new option named __New Relic__ in admin menu. In case of WPMU, menu option could be found in network dashboard.

### Capture URL

If Capture URLs setting is enabled, it will capture URL parameters for displaying in transaction traces.

![screenshot from 2016-11-07 01 40 25](https://cloud.githubusercontent.com/assets/2941333/20041170/60d41e3a-a48b-11e6-9282-3f12d874606f.png)

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
Ex: in New Relic Insights you can query transactions for non-logged in users as
```
SELECT * from Transaction where user = 'not-logged-in'
```

### Template

Template of current request will be set with `template` key.


### Theme

Current theme will be set with “theme” custom attribute.

```
SELECT * from Transaction where theme = 'Twenty Fifteen'
```

### Request type

Whether request is of type web/ajax/cli/ etc. can be determined using “request_type” custom attribute. Request type can be override using wp_nr_request_type filter.
Possible values are ajax, cron, cli, gearman, web.

```
SELECT * from Transaction where request_type = 'ajax'
```

### Transaction Name

Transaction name is getting set as per the main WP_Query parameters using [newrelic_name_transaction](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-name-wt). 
Possible values are Default Home Page, Front Page, Blog Page, Network Dashboard, Dashboard, Single - {post_type}, Page - {pagename}, Date Archive, Search Page, Feed, Archive - {post_type}, Category - {cat_name}, Tag - {tag_name}, Tax - {taxonomy} - {term}

### Custom Error Logging

Using __wp_nr_log_errors__ function, any plugin/theme can log errors/notices to New Relic for current transaction. 
Note: This function can be used more than once but only last use will be considered to log the error to New Relic. It’s limitation of [PHP Agent API](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-notice-error).

```
wp_nr_log_errors( 'Got this error', Exception $exception );
```

### Runtime of async tasks

This plugin also tracks runtime of [gearman](https://github.com/10up/WP-Gears) async tasks. Gearman async task run for a particular hook and it’s runtime can be track using “wp_async_task-{hook}” custom attribute in New Relic Insights.

## Development

### Issues

If you identify any errors or have an idea for improving the plugin, please open an [issue](https://github.com/10up/wp-newrelic/issues?stage=open). We're excited to see what the community thinks of this project, and we would love your input!

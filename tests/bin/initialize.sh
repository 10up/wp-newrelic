#!/bin/bash
npm run env run tests-wordpress chmod -- -c ugo+w /var/www/html
npm run env run tests-cli wp rewrite structure '/%postname%/' -- --hard

## Install New Relic PHP agent
# Step 1: Update package lists
npm run env run tests-wordpress -- sudo apt-get update

# Step 2: Install wget
npm run env run tests-wordpress -- sudo apt-get install -y wget

# Step 3: Download the New Relic agent
npm run env run tests-wordpress -- sudo wget -L -O newrelic-php5-11.5.0.18-linux.tar.gz https://download.newrelic.com/php_agent/archive/11.5.0.18/newrelic-php5-11.5.0.18-linux.tar.gz

# Step 4: Extract and install
npm run env run tests-wordpress -- tar -xzf newrelic-php5-11.5.0.18-linux.tar.gz
npm run env run tests-wordpress sudo NR_INSTALL_USE_CP_NOT_LN=1 NR_INSTALL_SILENT=0 ./newrelic-php5-11.5.0.18-linux/newrelic-install install

# Step 5: Restart the web server
npm run env run tests-wordpress sudo /etc/init.d/apache2 reload

#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

# Paths relative to /content.

# WordPress.
vendor/bin/wp core install --path=$WP_WEBROOT --url=localhost:8000 \
	--title=RS --skip-email \
	--admin_email=wordpress@example.com \
	--admin_user=wordpress --admin_password=password

# Replace wp-content with this project's checkout.
cd $(composer config extra.wordpress-install-dir) && rm -fr wp-content && ln -s $TRAVIS_BUILD_DIR wp-content && cd -

vendor/bin/wp --path=$WP_WEBROOT rewrite flush

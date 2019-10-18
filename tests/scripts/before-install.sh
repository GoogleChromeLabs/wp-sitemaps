#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

# Database.
mysqladmin create wordpress_test -u root -p$MYSQL_ROOT_PASSWORD -h docker_db_1
mysqladmin create wordpress_app -u root -p$MYSQL_ROOT_PASSWORD -h docker_db_1

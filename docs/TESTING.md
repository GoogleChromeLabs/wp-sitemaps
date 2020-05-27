# TESTING

[TODO: Add this]

If your site is not running under http://sitemaps.local then you can run the functional tests against the
site as follows:

```bash
#hardcode it
export WP_SITEURL=http://sitemaps.local 
composer run test:behat-local

#Any vagrant install with wp cli
export WP_SITEURL=$(vagrant ssh -c "cd /vagrant && wp option get home") 
composer run test:behat-local
```

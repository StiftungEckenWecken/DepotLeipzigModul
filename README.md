# Depot
Buchungsplattform für das Teilen von Ressourcen

## Requirements
> [Drupal 7](http://drupal.org/) & [BAT module](http://drupal.org/project/bat) - console access - MySQL DB

> We recommend PHP7. Ensure in php.ini that "php_intl.dll" and "curl" extensions are running.

## Installation
> 1. Install [Drush](http://drush.org) and [Composer](https://getcomposer.org/download/)
> 2. If not yet done: Install Drupal 7, e.g. via CLI/Drush ([Howto](http://docs.drush.org/en/master/install/))
> 3. Run *drush make --no-core sites/all/modules/bat/bat.make*
> 4. Visit */admin/modules* and activate Depot- and Composer-manager-module ([Link](https://www.drupal.org/project/composer_manager)
> 5. In  *sites/default/files/composer* run *composer install*
> 5. Ensure succesful installation by following the steps in the official [BAT for Drupal guide](http://docs.roomify.us/bat/drupal/installation.html)
> 6. Copy config.default.inc and rename it to config.inc. Add API keys
> * Congratulations, you are done! *

## Misc

> For best practice round'about Drupal modules check the [example module repository](http://cgit.drupalcode.org/examples/tree/)

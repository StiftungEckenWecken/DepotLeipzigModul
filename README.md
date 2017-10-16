# DepotLeipzigerWesten
Buchungsplattform für das Teilen von Ressourcen

## Requirements
> [Drupal 7](http://drupal.org/) & [BAT module](http://drupal.org/project/bat) - console access - MySQL DB

## Installation
> 1. Install [Drush](http://drush.org) and [Composer](https://getcomposer.org/download/)
> 2. If not yet done: Install Drupal 7 via Web-Frontend or CLI/Drush ([Howto](http://docs.drush.org/en/master/install/))
> 3. Run *drush make --no-core sites/all/modules/bat/bat.make*
> 4. Visit */admin/modules* and activate Depot- and Composer-manager-module ([Link](https://www.drupal.org/project/composer_manager)
> 5. In  *sites/default/files/composer* run *composer install*
> 5. Ensure succesful installation by following the steps in the official [BAT for Drupal guide](http://docs.roomify.us/bat/drupal/installation.html)

## Misc

> No tests yet available
> For best practice check the [example module](http://cgit.drupalcode.org/examples/tree/)
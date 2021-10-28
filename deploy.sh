#!/bin/bash

printf "==========================================\n"
printf "Deploy From Repository Using Git Webhooks\n"
printf "==========================================\n"

printf "Entering Maintenance Mode... \n"
php artisan down

printf "Installing PHP Dependencies... \n"
export COMPOSER_HOME="$HOME/.config/composer";
php /usr/local/bin/composer install --no-interaction --no-dev --prefer-dist

printf "Pulling Master Update From Repository... \n"
git reset --hard
git pull

printf "Clearing Cache... \n"
php artisan permission:cache-reset
php artisan config:clear
php artisan cache:clear
php /usr/local/bin/composer dump-autoload
php artisan view:clear
php artisan route:clear

printf "Migrating Database... \n"
php artisan migrate --force

printf "Fixing Folder and File Permission.. \n"
find -type f -exec chmod 644 -R {} \;
find -type d -exec chmod 755 -R {} \;

printf "Finishing Deploy And Bring up Application... \n"
php artisan up

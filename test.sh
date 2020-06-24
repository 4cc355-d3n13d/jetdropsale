#!/usr/bin/env bash
php artisan cache:clear
php artisan config:clear
php artisan l5-swagger:generate
vendor/phpunit/phpunit/phpunit
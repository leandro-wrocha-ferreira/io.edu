#!/bin/sh

composer install --prefer-dist --optimize-autoloader

php index.php Migrations migrate

nginx

exec php-fpm

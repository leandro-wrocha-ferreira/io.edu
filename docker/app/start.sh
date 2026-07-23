#!/bin/sh

composer install --prefer-dist --optimize-autoloader

nginx

exec php-fpm

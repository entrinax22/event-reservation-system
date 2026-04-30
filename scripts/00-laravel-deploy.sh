#!/usr/bin/env bash

php artisan migrate --force
php artisan storage:link

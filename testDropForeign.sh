#!/usr/bin/env bash

echo -n > database/database.sqlite
php artisan test:foreignDrop

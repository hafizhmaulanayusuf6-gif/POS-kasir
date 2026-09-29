#!/bin/bash

# Clear dan cache konfigurasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otomatis migrasi database
php artisan migrate --force

# Mulai server web
apache2-foreground
#!/bin/sh
set -e

# Czekaj aż volume będzie dostępny
echo "Waiting for database directory..."
while [ ! -d "/var/www/html/database" ]; do
  sleep 1
done

# Upewnij się że katalog istnieje
mkdir -p /var/www/html/database

# Uruchom migracje
echo "Running migrations..."
php artisan migrate --force

# Startuj serwer
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT}

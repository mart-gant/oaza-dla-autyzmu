#!/bin/sh
set -e

# Czekaj aż volume będzie dostępny
echo "Waiting for database directory..."
while [ ! -d "/var/www/html/database" ]; do
  sleep 1
done

# Upewnij się że katalog istnieje
mkdir -p /var/www/html/database

# Usuń stary plik bazy danych jeśli istnieje
if [ -f "/var/www/html/database/database.sqlite" ]; then
  echo "Removing old database file..."
  rm -f /var/www/html/database/database.sqlite
fi

# Uruchom migracje
echo "Running migrations..."
php artisan migrate --force

# Startuj serwer
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT}

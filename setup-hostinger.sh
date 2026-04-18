#!/bin/bash
# Skrypt instalacyjny do uruchomienia na serwerze Hostinger - SSH
# Uruchom: bash setup-hostinger.sh

echo "================================"
echo "Konfiguracja projektu na Hostinger"
echo "================================"
echo ""

# Sprawdzenie czy jesteśmy w public_html
if [ ! -f "artisan" ]; then
    echo "❌ Błąd: Uruchom skrypt z folderu gdzie jest artisan!"
    exit 1
fi

echo "✓ Projekt znaleziony"
echo ""

# 1. Instalacja Composer zależności (jeśli brakuje vendor)
echo "1️⃣  Sprawdzam Composer zależności..."
if [ ! -d "vendor" ]; then
    echo "   Instaluję composer install..."
    composer install --no-dev --optimize-autoloader
    if [ $? -ne 0 ]; then
        echo "❌ Błąd: Composer install nie powiódł się"
        exit 1
    fi
else
    echo "   ✓ Vendor już istnieje"
fi
echo ""

# 2. Zmień .env.production na .env
echo "2️⃣  Przygotowuję .env..."
if [ -f ".env.production" ]; then
    mv .env.production .env
    echo "   ✓ Zmieniono .env.production na .env"
elif [ ! -f ".env" ]; then
    echo "❌ Błąd: Brak .env ani .env.production"
    exit 1
fi
echo ""

# 3. Wygeneruj APP_KEY
echo "3️⃣  Generuję APP_KEY..."
php artisan key:generate --force
echo "   ✓ APP_KEY wygenerowany"
echo ""

# 4. Nastaw uprawnienia do storage
echo "4️⃣  Nastaw uprawnienia do storage..."
chmod -R 755 bootstrap/
chmod -R 755 bootstrap/cache/
chmod -R 755 storage/
chmod -R 755 storage/logs/
chmod -R 755 storage/framework/
chmod -R 755 storage/framework/views/
chmod -R 755 storage/framework/cache/
echo "   ✓ Uprawnienia ustawione"
echo ""

# 5. Uruchom migracje bazy danych
echo "5️⃣  Uruchamiam migracje bazy danych..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "❌ Błąd: Migracje nie powiodły się"
    echo "   Sprawdź storage/logs/laravel.log"
    exit 1
fi
echo "   ✓ Migracje zakończone"
echo ""

# 6. Opcjonalnie: Seeduj bazę (jeśli chcesz przykładowe dane)
echo "6️⃣  Czy chcesz załadować przykładowe dane? (y/n)"
read -r load_seeds
if [ "$load_seeds" = "y" ] || [ "$load_seeds" = "Y" ]; then
    php artisan db:seed --force
    echo "   ✓ Baza załadowana przykładowymi danymi"
else
    echo "   ✓ Pominięto załadowanie danych"
fi
echo ""

# 7. Wyczyść cache
echo "7️⃣  Czyszczę cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   ✓ Cache wyczyszczony"
echo ""

# 8. Testuj połączenie z bazą
echo "8️⃣  Testuję połączenie z bazą danych..."
php artisan tinker <<EOF
DB::statement('SELECT 1');
'Połączenie z bazą OK'
exit;
EOF

if [ $? -eq 0 ]; then
    echo "   ✓ Połączenie z bazą pracuje"
else
    echo "⚠️  Błąd połączenia z bazą - sprawdź .env"
    exit 1
fi
echo ""

# 9. Wyświetl informacje
echo "9️⃣  Informacje o aplikacji:"
php artisan about
echo ""

echo "================================"
echo "✅ INSTALACJA ZAKOŃCZONA!"
echo "================================"
echo ""
echo "📝 Sprawdzenie:"
echo "   1. Otwórz https://twoja-domena.com w przeglądarce"
echo "   2. Kliknij 'Register' i utwórz konto"
echo "   3. Sprawdź czy wszystko działa"
echo ""
echo "📋 Jeśli są błędy:"
echo "   tail -100 storage/logs/laravel.log"
echo ""
echo "🔒 Uprawnienia:"
echo "   chown -R twoj_login:twoj_login storage/"
echo "   chown -R twoj_login:twoj_login bootstrap/cache/"
echo ""

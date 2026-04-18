#!/bin/bash
# Skrypt przygotowujący projekt do wdrażania na Hostinger
# Uruchom: bash prepare-for-hostinger.sh

echo "================================"
echo "Przygotowywanie projektu do Hostinger"
echo "================================"
echo ""

# Sprawdzź czy jesteśmy w głównym folderze projektu
if [ ! -f "artisan" ]; then
    echo "❌ Błąd: Uruchom skrypt z głównego folderu projektu!"
    exit 1
fi

echo "✓ Projekt znaleziony"
echo ""

# 1. Zainstaluj Composer zależności
echo "1️⃣  Instaluję Composer zależności..."
composer install --optimize-autoloader --no-dev
if [ $? -ne 0 ]; then
    echo "❌ Błąd: Composer install nie powiódł się"
    exit 1
fi
echo "✓ Composer zainstalowany"
echo ""

# 2. Zainstaluj Node.js zależności
echo "2️⃣  Instaluję Node.js zależności..."
npm install
if [ $? -ne 0 ]; then
    echo "❌ Błąd: npm install nie powiódł się"
    exit 1
fi
echo "✓ Node.js zainstalowany"
echo ""

# 3. Zbuduj frontend
echo "3️⃣  Budując frontend (Vite)..."
npm run build
if [ $? -ne 0 ]; then
    echo "❌ Błąd: npm run build nie powiódł się"
    exit 1
fi
echo "✓ Frontend zbudowany"
echo ""

# 4. Przygotuj .env production
echo "4️⃣  Przygotowuję .env.production..."
if [ ! -f ".env.example" ]; then
    echo "❌ Błąd: .env.example nie znaleziony"
    exit 1
fi

cp .env.example .env.production

# Modyfikuj .env.production dla production
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env.production
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env.production
sed -i 's|APP_URL=.*|APP_URL=https://twoja-domena.com|' .env.production
sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=file/' .env.production
sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=cookie/' .env.production
sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env.production

echo "✓ .env.production przygotowany"
echo ""
echo "📝 Pamiętaj do edytu .env.production PRZED uploadem:"
echo "   - APP_URL = https://twoja-domena.com"
echo "   - DB_HOST = localhost"
echo "   - DB_DATABASE = twoj_login_oaza"
echo "   - DB_USERNAME = twoj_login_oaza_user"
echo "   - DB_PASSWORD = HASŁO_Z_CPANEL"
echo ""

# 5. Wyczyść zbędne foldery
echo "5️⃣  Czyszczę zbędne foldery..."
rm -rf storage/logs/*.log 2>/dev/null || true
rm -rf bootstrap/cache/* 2>/dev/null || true
rm -rf node_modules/.cache/ 2>/dev/null || true
echo "✓ Czyszczenie zakończone"
echo ""

# 6. Sprawdzenie rozmiaru projektu
echo "6️⃣  Rozmiar do uploadowania:"
du -sh vendor/ 2>/dev/null && echo "   vendor/"
du -sh public/build/ 2>/dev/null && echo "   public/build/"
du -sh . --exclude=node_modules --exclude=.git 2>/dev/null
echo ""

echo "✅ PRZYGOTOWANIE ZAKOŃCZONE!"
echo ""
echo "📦 Co teraz uploadować na Hostinger:"
echo "   - app/"
echo "   - bootstrap/ (bez cache/)"
echo "   - config/"
echo "   - database/"
echo "   - public/build/"
echo "   - resources/"
echo "   - routes/"
echo "   - public/ (z .htaccess)"
echo "   - vendor/ (jeśli masz limit rozmiaru - wygeneruj na serwerze)"
echo "   - artisan"
echo "   - composer.json"
echo "   - composer.lock"
echo "   - package.json"
echo "   - package-lock.json"
echo "   - vite.config.js"
echo "   - tailwind.config.js"
echo "   - postcss.config.js"
echo "   - .env.production (po edycji!)"
echo ""
echo "💡 Instrukcje: przeczytaj HOSTINGER_DEPLOYMENT.md"
echo ""

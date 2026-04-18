@echo off
REM Skrypt przygotowujący projekt do wdrażania na Hostinger (Windows)
REM Uruchom: prepare-for-hostinger.bat

setlocal enabledelayedexpansion

echo ================================
echo Przygotowywanie projektu do Hostinger
echo ================================
echo.

REM Sprawdź czy jesteśmy w głównym folderze projektu
if not exist "artisan" (
    echo X Blad: Uruchom skrypt z glownego folderu projektu!
    pause
    exit /b 1
)

echo [+] Projekt znaleziony
echo.

REM 1. Zainstaluj Composer zależności
echo 1. Instaluje Composer zaleznosci...
call composer install --optimize-autoloader --no-dev
if errorlevel 1 (
    echo X Blad: Composer install nie powiodl sie
    pause
    exit /b 1
)
echo [+] Composer zainstalowany
echo.

REM 2. Zainstaluj Node.js zależności
echo 2. Instaluje Node.js zaleznosci...
call npm install
if errorlevel 1 (
    echo X Blad: npm install nie powiodl sie
    pause
    exit /b 1
)
echo [+] Node.js zainstalowany
echo.

REM 3. Zbuduj frontend
echo 3. Buduje frontend (Vite)...
call npm run build
if errorlevel 1 (
    echo X Blad: npm run build nie powiodl sie
    pause
    exit /b 1
)
echo [+] Frontend zbudowany
echo.

REM 4. Przygotuj .env production
echo 4. Przygotowuje .env.production...
if not exist ".env.example" (
    echo X Blad: .env.example nie znaleziony
    pause
    exit /b 1
)

copy ".env.example" ".env.production" >nul
echo [+] .env.production przygotowany
echo.

echo. > temp.txt
echo Pamietaj do edytu .env.production PRZED uploadem:
echo    - APP_URL = https://twoja-domena.com
echo    - DB_HOST = localhost
echo    - DB_DATABASE = twoj_login_oaza
echo    - DB_USERNAME = twoj_login_oaza_user
echo    - DB_PASSWORD = HASLO_Z_CPANEL
echo.

REM 5. Informacja o rozmiarach
echo 5. Informacja o rozmiarze projektu:
echo    (Folder vendor: )
for /f %%A in ('dir /s /b vendor 2^>nul ^| find /c /v ""') do echo    %%A plikow w vendor

echo.
echo ====================================
echo PRZYGOTOWANIE ZAKONCZONE!
echo ====================================
echo.

echo Co teraz uploadowac na Hostinger:
echo  - app/
echo  - bootstrap/ (bez cache/)
echo  - config/
echo  - database/
echo  - public/build/
echo  - resources/
echo  - routes/
echo  - public/ (z .htaccess)
echo  - vendor/ (jeśli masz limit - wygeneruj na serwerze)
echo  - artisan
echo  - composer.json
echo  - composer.lock
echo  - package.json
echo  - .env.production (po edycji!)
echo.

echo Instrukcje: przeczytaj HOSTINGER_DEPLOYMENT.md
echo.

pause

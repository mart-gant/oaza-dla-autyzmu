# Wdrażanie Oaza dla Autyzmu na Hostinger - Przewodnik Krok po Kroku

## 📋 Spis treści
1. [Sprawdzenie wymagań](#sprawdzenie-wymagań)
2. [Przygotowanie projektu lokalnie](#przygotowanie-projektu-lokalnie)
3. [Konfiguracja na Hostinger](#konfiguracja-na-hostinger)
4. [Upload projektu](#upload-projektu)
5. [Konfiguracja bazy danych](#konfiguracja-bazy-danych)
6. [Instalacja zależności na serwerze](#instalacja-zależności-na-serwerze)
7. [Konfiguracja aplikacji](#konfiguracja-aplikacji)
8. [Testowanie w przeglądarce](#testowanie-w-przeglądarce)
9. [Troubleshooting](#troubleshooting)

---

## 1. Sprawdzenie wymagań

### Wymagania dla Hostinger:
- **PHP 8.3+** (dostępne na Hostinger)
- **MySQL 8.0+** (dostępne na Hostinger)
- **Composer** (dostępny na Hostinger)
- **Node.js** (dostępny na Hostinger)
- Dostęp do **SSH** (wymagany do zainstalowania zależności)
- Dostęp do **cPanel** (zarządzanie bazą i plikami)

### Sprawdzenie w cPanel:
1. Zaloguj się do **cPanel** (zwykle na porcie :2083)
2. Idź do **Software** → **MultiPHP Manager**
3. Upewnij się że masz **PHP 8.3** dostępny
4. Sprawdź moduły: `curl`, `gd`, `mbstring`, `mysqlnd`, `openssl`, `zip` (powinny być domyślnie)

---

## 2. Przygotowanie projektu lokalnie

### Krok 1: Zbuduj projekt finalnie
```bash
cd c:\Users\marty\Herd\oaza-dla-autyzmu

# Wychylij stary build
rm -r public/build
rm -r vendor
rm -r node_modules

# Zainstaluj zależności
composer install --optimize-autoloader --no-dev

# Zainstaluj frontend
npm install

# Zbuduj frontend
npm run build
```

### Krok 2: Przygotuj plik .env do wdrażania
```bash
# Skopiuj .env.example
cp .env.example .env.production

# Edytuj .env.production - ustaw:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://twoja-domena.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=oaza_dla_autyzmu
DB_USERNAME=oaza_user
DB_PASSWORD=SILNE_HASŁO_Z_CPANEL

CACHE_DRIVER=file
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
```

### Krok 3: Przygotuj foldery do uploadowania
```bash
# Najlepiej uploaduj bez tych folderów (będą generowane):
# - storage/
# - bootstrap/cache/
# - node_modules/
# - vendor/

# Możesz zrobić archiwum z głównymi plikami:
# - app/
# - bootstrap/
# - config/
# - database/
# - public/build/   (zbudowany frontend!)
# - resources/
# - routes/
# - .env.production
# - artisan
# - composer.json
# - composer.lock
# - package.json
# - package-lock.json
# - vite.config.js
# - postcss.config.js
# - tailwind.config.js
# - phpunit.xml
# - .htaccess (dla Apache w public/)
```

---

## 3. Konfiguracja na Hostinger

### Krok 1: Zaloguj się do cPanel
- https://twoja-domena.com:2083
- Login: login cPanel
- Hasło: hasło cPanel

### Krok 2: Ustaw domyślny adres w document root (public folder)
1. Idź do **File Manager**
2. Przejdź do folderu **public_html**
3. Upewnij się że zawiera Twojego projektu `public/` folder
4. **Lub** poprzez **SSH** (lepiej) - zajrzyj do kroku 4

### Krok 3: Ustaw wersję PHP na 8.3
1. Idź do **Software** → **MultiPHP Manager**
2. Wybierz domenę/folder `public_html`
3. Zmień na **PHP 8.3**
4. Zapisz

### Krok 4: Skonfiguruj SSH dostęp (jeśli nie masz)
1. Idź do **SSH Access**
2. Jeśli jest czerwony przycisk "Manage SSH Keys" - kliknij go
3. Wygeneruj nowy klucz SSH lub użyj istniejącego
4. Pobierz plik klucza prywatnego (`.key`)
5. Otwórz terminal na swoim komputerze:

```bash
# Zmień uprawnienia klucza
chmod 600 twój-klucz.key

# SSH do serwera
ssh -i twój-klucz.key twój_login@twoja.domena.com

# Lub z IP serwera
ssh -i twój-klucz.key twój_login@IP_SERWERA
```

---

## 4. Upload projektu

### Opcja A: Przez SSH (REKOMENDOWANE)

```bash
# Na lokalnym komputerze
cd c:\Users\marty\Herd\oaza-dla-autyzmu

# Nawiąż SSH połączenie
ssh -i twój-klucz.key twój_login@twoja.domena.com

# Przejdź do public_html
cd public_html

# Skopiuj pliki projektu (z SSH)
# Alternatywnie możesz użyć SCP:
scp -r -i twój-klucz.key \
  c:\Users\marty\Herd\oaza-dla-autyzmu/* \
  twój_login@twoja.domena.com:~/public_html/
```

### Opcja B: Przez SFTP (FileZilla)

1. Download **FileZilla** - https://filezilla-project.org/
2. **File** → **Site Manager**
3. Nowa strona:
   - **Host**: twoja.domena.com
   - **Protocol**: SFTP - SSH File Transfer Protocol
   - **Port**: 22
   - **User**: twój_login cPanel
   - **Password**: hasło cPanel
   - **Key file**: (jeśli używasz SSH key)
4. Connect
5. Lewy panel (local) - idź do `c:\Users\marty\Herd\oaza-dla-autyzmu`
6. Prawy panel (remote) - idź do `public_html`
7. Uploaduj folderami:
   - `app/`
   - `bootstrap/` (bez podfolderu cache/)
   - `config/`
   - `database/`
   - `public/build/` (ALE UWAGA - patrz krok 5!)
   - `resources/`
   - `routes/`
   - Pliki: `artisan`, `composer.json`, `composer.lock`, `package.json`, `.env.production`

---

## 5. Struktura folderów na Hostinger

**WAŻNE**: Hostinger domyślnie serwuje z folderu `public_html`

**Poprawna struktura:**

```
public_html/
├── public/          ← Tu są tylko statyczne pliki!
│   ├── build/       ← Frontend (CSS, JS)
│   ├── index.php    ← Punkt wejścia
│   └── .htaccess    ← Przekierowania
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/         ← Będzie wygenerowany
├── vendor/          ← Będzie wygenerowany
├── artisan
├── composer.json
├── composer.lock
└── .env.production  ← Zmień na .env!
```

**Lub struktura zaawansowana** (jeśli masz dostęp do /root):

```
/home/twoj_login/
├── public_html/
│   └── public/      ← Tylko zawartość public/!
├── app/
├── bootstrap/
├── config/
├── ... (reszta projektu)
```

Jeśli zrobisz drugą strukturę, należy w .htaccess zmienić ścieżki.

---

## 6. Konfiguracja bazy danych

### Krok 1: Utwórz bazę danych w cPanel

1. Zaloguj się do **cPanel**
2. Idź do **MySQL Databases**
3. Utwórz nową bazę:
   - **Database Name**: `twoj_login_oaza` (np. `marty_oaza`)
   - Zapisz pełną nazwę!

### Krok 2: Utwórz użytkownika MySQL

1. W **MySQL Databases** → **MySQL Users**
2. **Add New User**:
   - **Username**: `twoj_login_oaza_user` (np. `marty_oaza_user`)
   - **Password**: Generuj silne hasło → **Create User**
3. Przyznaj uprawnienia:
   - Zaznacz użytkownika i bazę
   - Uprawnienia: **ALL PRIVILEGES**
   - **Make Changes**

**Zapisz sobie:**
- DB_HOST: `localhost` (zwykle, ale może być inny - sprawdź w cPanel)
- DB_DATABASE: `twoj_login_oaza`
- DB_USERNAME: `twoj_login_oaza_user`
- DB_PASSWORD: Hasło które wygenerowałeś

---

## 7. Instalacja zależności na serwerze

### Krok 1: Zaloguj się przez SSH

```bash
ssh -i twój-klucz.key twój_login@twoja.domena.com
cd public_html
```

### Krok 2: Zainstaluj Composer zależności

```bash
composer install --no-dev --optimize-autoloader
```

Jeśli `composer` nie jest dostępny:

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/home/twoj_login/bin --filename=composer
php composer.phar install --no-dev --optimize-autoloader
```

### Krok 3: Zainstaluj Node.js zależności (jeśli trzeba)

```bash
node -v
npm -v

# Jeśli zrobisz build lokalnie, możesz pominąć:
# npm install
# npm run build
```

### Krok 4: Zmień nazwę .env

```bash
mv .env.production .env

# Edytuj .env - upewnij się że dane bazowe są poprawne:
nano .env

# Zmień te linie:
DB_HOST=localhost
DB_DATABASE=twoj_login_oaza
DB_USERNAME=twoj_login_oaza_user
DB_PASSWORD=HASŁO_Z_CPANEL
APP_URL=https://twoja-domena.com
```

---

## 8. Konfiguracja aplikacji

### Krok 1: Wygeneruj APP_KEY

```bash
php artisan key:generate --force
```

### Krok 2: Nastaw uprawnienia do plików

```bash
chmod 755 bootstrap/
chmod 755 storage/
chmod 755 storage/logs/
chmod 755 storage/framework/
chmod 755 storage/framework/views/
chmod 755 storage/framework/cache/
```

### Krok 3: Uruchom migracje bazy danych

```bash
php artisan migrate --force
```

Jeśli chcesz załadować przykładowe dane:

```bash
php artisan db:seed --force
```

### Krok 4: Wyczyść cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Krok 5: Ustaw uprawnienia do storage

```bash
chown -R twoj_login:twoj_login storage/
chown -R twoj_login:twoj_login bootstrap/cache/
```

---

## 9. Konfiguracja Apache (.htaccess)

Plik `.htaccess` powinien być w folderze `public/` lub `public_html/`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Ze ścieżką do public:**

Jeśli struktura to:
```
public_html/
├── public/
│   ├── index.php
│   └── .htaccess
```

To `.htaccess` w `public_html/` powinien być:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## 10. Testowanie w przeglądarce

### Krok 1: Sprawdzenie aplikacji

Otwórz w przeglądarce:
```
https://twoja-domena.com
```

Jeśli widzisz błąd, sprawdź:

1. **Błąd 500** - zajrzyj do `storage/logs/laravel.log`:
```bash
tail -100 storage/logs/laravel.log
```

2. **Błąd 404** - sprawdź `.htaccess` i strukturę plików

3. **Baza danych** - testuj połączenie:
```bash
php artisan tinker
DB::connection()->getPdo();  # Powinno zwrócić połączenie
```

### Krok 2: Sprawdzenie struktury

```bash
php artisan about
```

Powinno pokazać:
- ✓ Application Name: Oaza dla Autyzmu
- ✓ Laravel Version: 12.x
- ✓ PHP Version: 8.3.x
- ✓ Database: Connected
- ✓ Environment: production

### Krok 3: Rejestracja i logowanie

1. Kliknij **Register**
2. Utwórz konto
3. Zaloguj się
4. Sprawdź czy widać dashboard

---

## 11. Troubleshooting

### ❌ "No application encryption key has been set"
```bash
php artisan key:generate --force
php artisan config:cache
```

### ❌ "SQLSTATE[HY000]: General error: 1030"
```bash
# Baza danych nie istnieje lub brak połączenia
# Sprawdź w cPanel czy baza istnieje
# Sprawdź .env czy dane są poprawne
php artisan tinker
DB::statement('SELECT 1');
```

### ❌ "Class 'PDO' not found"
```bash
# Brak PHP MySQL extension
# W cPanel: Software → MultiPHP → upewnij się że PHP ma mysql extension
```

### ❌ "No space left on device"
```bash
# Dysk pełny
df -h
# Oczyść storage:
rm -rf storage/logs/*.log
php artisan storage:link  # jeśli trzeba
```

### ❌ Strona nie ładuje CSS/JS
```bash
# Problem z public/build
# Upewnij się że npm run build się wykonał lokalnie
# Sprawdź czy public/build zawiera pliki
ls -la public/build/
```

### ❌ "CORS error" lub API nie działa
```bash
# Sprawdź CORS_ALLOWED_ORIGINS w .env
# Ustawić APP_URL na pełny URL z https://
```

### ❌ Maile się nie wysyłają
```bash
# W .env ustaw:
MAIL_DRIVER=log  # Testowo: sprawdzi w storage/logs/
# Lub skonfiguruj SMTP:
MAIL_HOST=smtp.twojego-hostinger.pl
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=info@twoja-domena.com
```

---

## 12. Optymalizacja (Po wdrożeniu)

```bash
# Optymalizuj autoloader Composera
composer dump-autoload --optimize

# Cache konfiguracji
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Wyczyść stary cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## ✅ Checklist wdrażania

- [ ] Sprawdziłem wymagania na Hostinger (PHP 8.3)
- [ ] Zbudowałem projekt lokalnie (`npm run build`)
- [ ] Uploadowałem pliki na Hostinger
- [ ] Utworzyłem bazę danych w cPanel
- [ ] Zainstalowałem Composer zależności na serwerze
- [ ] Ustawiłem .env z poprawnym URL i danymi bazy
- [ ] Wygenerowałem APP_KEY
- [ ] Uruchomiłem migracje (`php artisan migrate`)
- [ ] Sprawdziłem storage/logs/laravel.log czy są błędy
- [ ] Testowałem aplikację w przeglądarce
- [ ] Zarejestrował się nowy użytkownik
- [ ] Sprawdziłem że logowanie działa
- [ ] Forum, placówki, artykuły są dostępne
- [ ] Maile są wysyłane (jeśli skonfigurowane)

---

Powodzenia z wdrażaniem! Jeśli pojawią się problemy - sprawdź `storage/logs/laravel.log` i podziel się komunikatem błędu 🚀

# Quick Start - Wdrażanie na Hostinger (Skrót)

## 📱 Struktura Hostinger Folder Structure

```
public_html/                  ← Root hosting
├── public/                   ← Statyka (CSS, JS, images)
│   ├── build/               ← Frontend (npm run build output)
│   ├── index.php            ← Punkt wejścia
│   └── .htaccess            ← Routing
├── app/, config/, routes/   ← Kod aplikacji
├── vendor/                  ← Composer (lub wygeneruje się)
├── storage/                 ← Logi, sessions (generator)
├── bootstrap/               ← Cache (generator)
├── artisan                  ← CLI
├── composer.json/.lock
└── .env                     ← SEKRET (bez tego)
```

---

## ⚡ SZYBKA INSTRUKCJA (7 KROKÓW)

### LOKALNIE (na Twoim komputerze):

```bash
cd c:\Users\marty\Herd\oaza-dla-autyzmu

# 1. Przygotuj projekt
prepare-for-hostinger.bat  # Windows
# lub
bash prepare-for-hostinger.sh  # Mac/Linux

# 2. Edytuj .env.production
# Zmień:
# APP_URL=https://twoja-domena.com
# DB_DATABASE=oaza_dla_autyzmu (z cPanel)
# DB_USERNAME=oaza_user (z cPanel)
# DB_PASSWORD=HASŁO (z cPanel)
```

### NA HOSTINGER (cPanel):

```bash
# 3. Utwórz bazę w cPanel
# MySQL Databases → Utwórz bazę: oaza_dla_autyzmu
# MySQL Users → Utwórz usera: oaza_user
# Przyznaj ALL PRIVILEGES

# 4. SSH do serwera (lub cPanel Terminal)
ssh -i klucz.key login@domena.com
cd public_html
```

```bash
# 5. Upload projektu (SFTP lub SCP)
scp -r app bootstrap config database public routes resources \
  artisan composer.json .env.production login@domena.com:~/public_html/
```

```bash
# 6. Na serwerze - zainstaluj i skonfiguruj
bash setup-hostinger.sh

# Lub ręcznie:
php artisan key:generate --force
php artisan migrate --force
php artisan config:cache
```

```bash
# 7. Sprawdź w przeglądarce
https://twoja-domena.com
```

---

## 🔑 Zmienne do pobrania z cPanel

1. **cPanel Login**: login_do_cpanel
2. **cPanel Hasło**: hasło_do_cpanel
3. **MySQL Host**: localhost (lub w cPanel)
4. **MySQL Database**: `login_oaza` (FULL NAME z cPanel!)
5. **MySQL User**: `login_oazauser` (z cPanel, np. marty_oazauser)
6. **MySQL Password**: SILNE HASŁO (z cPanel)
7. **SSH URL**: login@domena.com lub login@IP_SERWERA
8. **SSH Port**: 22 (domyślnie) lub inny (z cPanel)

---

## 📝 .env do wpisania na serwerze

Otwórz SSH i:

```bash
nano .env

# Zmień opcje:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://twoja-domena.com
APP_KEY=  # Będzie wygenerowany automatycznie

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=login_oaza         # Z CPANEL!
DB_USERNAME=login_oazauser     # Z CPANEL!
DB_PASSWORD=HASŁO_Z_CPANEL

CACHE_DRIVER=file
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
```

Zapisz: `Ctrl+X` → `Y` → `Enter`

---

## 🐛 Błędy i rozwiązania

| Błąd | Rozwiązanie |
|------|------------|
| "No application encryption key" | `php artisan key:generate --force` |
| SQLSTATE[HY000] (baza) | Sprawdź .env, czy DB istnieje w cPanel |
| Class 'PDO' not found | cPanel: Software → PHP → Extensions (mysql on) |
| 500 Internal Server Error | `tail -50 storage/logs/laravel.log` |
| CSS/JS się nie ładują | `npm run build` (lokalnie), check `public/build/` |
| "Storage is not writable" | `chmod -R 755 storage/ bootstrap/` |

---

## 📞 Potrzebne pliki

✓ `HOSTINGER_DEPLOYMENT.md` - Pełna dokumentacja  
✓ `prepare-for-hostinger.bat/sh` - Przygotowanie lokalnie  
✓ `setup-hostinger.sh` - Instalacja na serwerze  
✓ `public/.htaccess` - Routing Apache  

---

## ✅ Test w przeglądarce

1. Otwórz: `https://twoja-domena.com`
2. Kliknij: **Register**
3. Utwórz konto: imię, email, hasło
4. Zaloguj się
5. Sprawdź: Forum, Placówki, Artykuły

**Gotowe!** 🎉

---

Wszelkie problemy: czytaj `HOSTINGER_DEPLOYMENT.md` lub sprawdzaj `storage/logs/laravel.log`

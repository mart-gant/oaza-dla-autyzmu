# Deployment Laravel na Hetzner - Przewodnik Krok po Kroku

## Spis treści
1. [Zakup i konfiguracja serwera](#1-zakup-i-konfiguracja-serwera)
2. [Instalacja środowiska](#2-instalacja-środowiska)
3. [Konfiguracja bazy danych](#3-konfiguracja-bazy-danych)
4. [Deployment aplikacji](#4-deployment-aplikacji)
5. [Konfiguracja Nginx](#5-konfiguracja-nginx)
6. [SSL (Certbot)](#6-ssl-certbot)
7. [Konfiguracja ENV](#7-konfiguracja-env)
8. [Optymalizacja](#8-optymalizacja)

---

## 1. Zakup i konfiguracja serwera

### 1.1 Kup serwer VPS na Hetzner
- Wejdź na https://www.hetzner.com/cloud
- Wybierz serwer: **CX22** (2 vCPU, 4GB RAM) - ~€5.83/miesiąc
- System: **Ubuntu 24.04 LTS**
- Lokalizacja: **Falkenstein, Germany** (najbliżej Polski)
- Dodaj SSH key lub zapisz hasło root

### 1.2 Pierwsze logowanie
```bash
ssh root@TWOJ_IP_SERWERA
```

### 1.3 Aktualizacja systemu
```bash
apt update && apt upgrade -y
```

### 1.4 Dodaj użytkownika (bezpieczeństwo)
```bash
adduser deploy
usermod -aG sudo deploy
su - deploy
```

---

## 2. Instalacja środowiska

### 2.1 Zainstaluj PHP 8.3
```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common php8.3-mysql php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-redis
```

Sprawdź wersję:
```bash
php -v
```

### 2.2 Zainstaluj Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 2.3 Zainstaluj Nginx
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

Sprawdź status:
```bash
sudo systemctl status nginx
```

### 2.4 Zainstaluj PostgreSQL 17
```bash
sudo apt install -y postgresql postgresql-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### 2.5 Zainstaluj Node.js (dla Vite)
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

### 2.6 Zainstaluj Git
```bash
sudo apt install -y git
```

---

## 3. Konfiguracja bazy danych

### 3.1 Utwórz użytkownika i bazę danych
```bash
sudo -u postgres psql
```

W konsoli PostgreSQL:
```sql
CREATE DATABASE oaza_autyzmu;
CREATE USER oaza_user WITH PASSWORD 'SILNE_HASLO_TUTAJ';
GRANT ALL PRIVILEGES ON DATABASE oaza_autyzmu TO oaza_user;
\q
```

### 3.2 Testuj połączenie
```bash
psql -h localhost -U oaza_user -d oaza_autyzmu
# Wpisz hasło
# \q aby wyjść
```

---

## 4. Deployment aplikacji

### 4.1 Przygotuj katalog
```bash
sudo mkdir -p /var/www/oaza-dla-autyzmu
sudo chown -R deploy:www-data /var/www/oaza-dla-autyzmu
cd /var/www/oaza-dla-autyzmu
```

### 4.2 Sklonuj repozytorium
```bash
git clone https://github.com/mart-gant/oaza-dla-autyzmu.git .
```

Jeśli prywatne repo, wygeneruj SSH key:
```bash
ssh-keygen -t ed25519 -C "deploy@hetzner"
cat ~/.ssh/id_ed25519.pub
# Dodaj klucz do GitHub: Settings > SSH and GPG keys
```

### 4.3 Zainstaluj zależności
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 4.4 Ustaw uprawnienia
```bash
sudo chown -R deploy:www-data /var/www/oaza-dla-autyzmu
sudo chmod -R 755 /var/www/oaza-dla-autyzmu
sudo chmod -R 775 /var/www/oaza-dla-autyzmu/storage
sudo chmod -R 775 /var/www/oaza-dla-autyzmu/bootstrap/cache
```

---

## 5. Konfiguracja Nginx

### 5.1 Utwórz konfigurację
```bash
sudo nano /etc/nginx/sites-available/oaza-dla-autyzmu
```

Wklej:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name oaza-autyzmu.pl www.oaza-autyzmu.pl;
    root /var/www/oaza-dla-autyzmu/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5.2 Aktywuj konfigurację
```bash
sudo ln -s /etc/nginx/sites-available/oaza-dla-autyzmu /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 6. SSL (Certbot)

### 6.1 Zainstaluj Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 6.2 Uzyskaj certyfikat (WAŻNE: Ustaw A record domeny na IP serwera PRZED tym krokiem!)
```bash
sudo certbot --nginx -d oaza-autyzmu.pl -d www.oaza-autyzmu.pl
```

Wybierz opcję przekierowania HTTP → HTTPS (opcja 2)

### 6.3 Automatyczne odnawianie
```bash
sudo systemctl status certbot.timer
```

Test odnowienia:
```bash
sudo certbot renew --dry-run
```

---

## 7. Konfiguracja ENV

### 7.1 Utwórz plik .env
```bash
cd /var/www/oaza-dla-autyzmu
cp .env.example .env
nano .env
```

### 7.2 Ustaw zmienne (SKOPIUJ I DOSTOSUJ):
```env
APP_NAME="Oaza dla Autyzmu"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://oaza-autyzmu.pl

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=oaza_autyzmu
DB_USERNAME=oaza_user
DB_PASSWORD=TWOJE_HASLO_Z_KROKU_3

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=resend
RESEND_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=no-reply@twoja-domena.pl
MAIL_FROM_NAME="Oaza dla Autyzmu"
MAIL_TO_ADDRESS=kontakt@twoja-domena.pl
```

`MAIL_FROM_ADDRESS` musi należeć do domeny lub nadawcy zweryfikowanego w Resend.

### 7.3 Wygeneruj APP_KEY
```bash
php artisan key:generate
```

### 7.4 Migracje i seedery
```bash
php artisan migrate --force
php artisan db:seed --class=FacilitiesSeeder --force
php artisan db:seed --class=ForumCategorySeeder --force
php artisan db:seed --class=ArticleSeeder --force
```

### 7.5 Utwórz admin użytkownika
```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@oaza.pl',
    'password' => bcrypt('TwojeSilneHaslo123!'),
    'role' => 'admin',
    'email_verified_at' => now()
]);
exit
```

### 7.6 Optymalizacja
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 8. Optymalizacja

### 8.1 Supervisor (dla queue workers)
```bash
sudo apt install -y supervisor
sudo nano /etc/supervisor/conf.d/oaza-worker.conf
```

Wklej:
```ini
[program:oaza-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/oaza-dla-autyzmu/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/oaza-dla-autyzmu/storage/logs/worker.log
stopwaitsecs=3600
```

Uruchom:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start oaza-worker:*
```

### 8.2 Cron (dla schedule)
```bash
crontab -e
```

Dodaj:
```
* * * * * cd /var/www/oaza-dla-autyzmu && php artisan schedule:run >> /dev/null 2>&1
```

### 8.3 Firewall
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

### 8.4 Fail2Ban (ochrona przed atakami)
```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## 9. Automatyczny deployment (opcjonalnie)

### 9.1 Utwórz skrypt deploy
```bash
nano /home/deploy/deploy.sh
```

```bash
#!/bin/bash
cd /var/www/oaza-dla-autyzmu

# Pobierz najnowsze zmiany
git pull origin main

# Zainstaluj zależności
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Migracje
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Uprawnienia
sudo chown -R deploy:www-data /var/www/oaza-dla-autyzmu
sudo chmod -R 755 /var/www/oaza-dla-autyzmu
sudo chmod -R 775 /var/www/oaza-dla-autyzmu/storage
sudo chmod -R 775 /var/www/oaza-dla-autyzmu/bootstrap/cache

# Restart
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
sudo supervisorctl restart oaza-worker:*

echo "Deployment completed!"
```

Ustaw uprawnienia:
```bash
chmod +x /home/deploy/deploy.sh
```

Użycie:
```bash
./deploy.sh
```

---

## 10. Testy i monitoring

### 10.1 Sprawdź czy działa
```bash
curl -I https://oaza-autyzmu.pl
```

### 10.2 Logi Nginx
```bash
sudo tail -f /var/log/nginx/error.log
```

### 10.3 Logi Laravel
```bash
tail -f /var/www/oaza-dla-autyzmu/storage/logs/laravel.log
```

### 10.4 Status PHP-FPM
```bash
sudo systemctl status php8.3-fpm
```

---

## 11. Backup (WAŻNE!)

### 11.1 Utwórz skrypt backup
```bash
nano /home/deploy/backup.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/home/deploy/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup bazy danych
pg_dump -U oaza_user oaza_autyzmu > $BACKUP_DIR/db_$DATE.sql

# Backup plików
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/oaza-dla-autyzmu/storage/app

# Usuń stare backupy (starsze niż 7 dni)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

### 11.2 Cron dla backup (codziennie o 3:00)
```bash
crontab -e
```

Dodaj:
```
0 3 * * * /home/deploy/backup.sh >> /home/deploy/backup.log 2>&1
```

---

## 12. DNS (Cloudflare lub home.pl)

### 12.1 Ustaw A record
```
A    @             TWOJ_IP_SERWERA
A    www           TWOJ_IP_SERWERA
```

### 12.2 Jeśli używasz Cloudflare:
- Proxy status: **Proxied** (pomarańczowa chmurka)
- SSL/TLS mode: **Full (strict)**
- Always Use HTTPS: **On**

---

## 13. Troubleshooting

### Problem: 500 Error
```bash
sudo chmod -R 775 /var/www/oaza-dla-autyzmu/storage
php artisan config:clear
php artisan cache:clear
tail -f storage/logs/laravel.log
```

### Problem: 419 CSRF Error
```bash
php artisan config:clear
php artisan cache:clear
# Sprawdź SESSION_DRIVER=database w .env
```

### Problem: Brak połączenia z bazą
```bash
psql -h localhost -U oaza_user -d oaza_autyzmu
# Sprawdź hasło w .env
```

### Problem: SSL nie działa
```bash
sudo certbot renew --dry-run
sudo nginx -t
sudo systemctl restart nginx
```

---

## Koszty miesięczne

- **VPS CX22 (4GB RAM)**: €5.83
- **Domena .pl**: ~€5/rok (≈€0.42/miesiąc)
- **Razem**: ~€6.25/miesiąc

**VS Laravel Cloud**: ~$19/miesiąc (bez bezpłatnego okresu)

**OSZCZĘDNOŚĆ**: ~€10.50/miesiąc (55% taniej!)

---

## Pytania? Problemy?

Jeśli coś nie działa:
1. Sprawdź logi: `tail -f storage/logs/laravel.log`
2. Sprawdź Nginx: `sudo nginx -t`
3. Sprawdź PHP: `php -v`
4. Sprawdź permissions: `ls -la storage/`

Powodzenia! 🚀

#!/bin/bash

# Automatyczny skrypt deployment dla Oaza dla Autyzmu na Hetzner
# Autor: GitHub Copilot
# Data: 2026-01-21

set -e  # Zatrzymaj przy błędzie

echo "=========================================="
echo "🚀 Automatyczny deployment Oaza dla Autyzmu"
echo "=========================================="
echo ""

# Kolory dla outputu
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Funkcja do wyświetlania kroków
step() {
    echo -e "${GREEN}[KROK]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[UWAGA]${NC} $1"
}

error() {
    echo -e "${RED}[BŁĄD]${NC} $1"
}

# Zapytaj o hasło do bazy danych
echo -e "${YELLOW}Podaj silne hasło dla bazy danych PostgreSQL:${NC}"
read -s DB_PASSWORD
echo ""

if [ -z "$DB_PASSWORD" ]; then
    error "Hasło nie może być puste!"
    exit 1
fi

# Zapytaj o domenę (opcjonalnie)
echo -e "${YELLOW}Podaj domenę (naciśnij Enter aby pominąć):${NC}"
read DOMAIN

# ==========================================
# KROK 1: Aktualizacja systemu
# ==========================================
step "Aktualizacja systemu..."
apt update && apt upgrade -y

# ==========================================
# KROK 2: Instalacja podstawowych narzędzi
# ==========================================
step "Instalacja podstawowych narzędzi..."
apt install -y software-properties-common curl wget git unzip

# ==========================================
# KROK 3: Instalacja PHP 8.3
# ==========================================
step "Instalacja PHP 8.3..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common php8.3-pgsql \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd \
    php8.3-bcmath php8.3-redis php8.3-intl

php -v

# ==========================================
# KROK 4: Instalacja Composer
# ==========================================
step "Instalacja Composer..."
cd /tmp
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
composer --version

# ==========================================
# KROK 5: Instalacja Nginx
# ==========================================
step "Instalacja Nginx..."
apt install -y nginx
systemctl start nginx
systemctl enable nginx

# ==========================================
# KROK 6: Instalacja PostgreSQL
# ==========================================
step "Instalacja PostgreSQL..."
apt install -y postgresql postgresql-contrib
systemctl start postgresql
systemctl enable postgresql

# ==========================================
# KROK 7: Instalacja Node.js 20
# ==========================================
step "Instalacja Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
node -v
npm -v

# ==========================================
# KROK 8: Tworzenie użytkownika deploy
# ==========================================
step "Tworzenie użytkownika deploy..."
if ! id "deploy" &>/dev/null; then
    adduser --disabled-password --gecos "" deploy
    usermod -aG sudo deploy
    echo "deploy ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
fi

# ==========================================
# KROK 9: Konfiguracja bazy danych
# ==========================================
step "Tworzenie bazy danych..."
sudo -u postgres psql <<EOF
CREATE DATABASE oaza_autyzmu;
CREATE USER oaza_user WITH PASSWORD '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON DATABASE oaza_autyzmu TO oaza_user;
ALTER DATABASE oaza_autyzmu OWNER TO oaza_user;
\q
EOF

# ==========================================
# KROK 10: Klonowanie repozytorium
# ==========================================
step "Klonowanie repozytorium z GitHub..."
mkdir -p /var/www/oaza-dla-autyzmu
cd /var/www/oaza-dla-autyzmu

if [ ! -d ".git" ]; then
    git clone https://github.com/mart-gant/oaza-dla-autyzmu.git .
else
    git pull origin main
fi

# ==========================================
# KROK 11: Instalacja zależności
# ==========================================
step "Instalacja zależności PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

step "Instalacja zależności JavaScript..."
npm install

step "Budowanie assetów..."
npm run build

# ==========================================
# KROK 12: Konfiguracja .env
# ==========================================
step "Tworzenie pliku .env..."
cat > .env <<EOF
APP_NAME="Oaza dla Autyzmu"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://${DOMAIN:-localhost}
APP_TIMEZONE=Europe/Warsaw
APP_LOCALE=pl
APP_FALLBACK_LOCALE=pl

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=oaza_autyzmu
DB_USERNAME=oaza_user
DB_PASSWORD=$DB_PASSWORD

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=database
QUEUE_CONNECTION=database

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

MAIL_MAILER=resend
RESEND_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=no-reply@twoja-domena.pl
MAIL_FROM_NAME="Oaza dla Autyzmu"
MAIL_TO_ADDRESS=kontakt@twoja-domena.pl
EOF

# ==========================================
# KROK 13: Generowanie APP_KEY
# ==========================================
step "Generowanie APP_KEY..."
php artisan key:generate --force

# ==========================================
# KROK 14: Migracje i seedery
# ==========================================
step "Uruchamianie migracji..."
php artisan migrate --force

step "Seedowanie danych..."
php artisan db:seed --class=FacilitiesSeeder --force || true
php artisan db:seed --class=ForumCategorySeeder --force || true
php artisan db:seed --class=ArticleSeeder --force || true

# ==========================================
# KROK 15: Tworzenie użytkownika admin
# ==========================================
step "Tworzenie użytkownika admin..."
echo -e "${YELLOW}Podaj email dla konta admin:${NC}"
read ADMIN_EMAIL
echo -e "${YELLOW}Podaj hasło dla konta admin:${NC}"
read -s ADMIN_PASSWORD
echo ""

php artisan tinker --execute="
\$user = App\Models\User::where('email', '$ADMIN_EMAIL')->first();
if (!\$user) {
    App\Models\User::create([
        'name' => 'Admin',
        'email' => '$ADMIN_EMAIL',
        'password' => bcrypt('$ADMIN_PASSWORD'),
        'role' => 'admin',
        'email_verified_at' => now()
    ]);
    echo 'Admin utworzony!';
} else {
    echo 'Admin już istnieje!';
}
"

# ==========================================
# KROK 16: Optymalizacja
# ==========================================
step "Optymalizacja Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# ==========================================
# KROK 17: Uprawnienia
# ==========================================
step "Ustawianie uprawnień..."
chown -R deploy:www-data /var/www/oaza-dla-autyzmu
chmod -R 755 /var/www/oaza-dla-autyzmu
chmod -R 775 /var/www/oaza-dla-autyzmu/storage
chmod -R 775 /var/www/oaza-dla-autyzmu/bootstrap/cache

# ==========================================
# KROK 18: Konfiguracja Nginx
# ==========================================
step "Konfiguracja Nginx..."
cat > /etc/nginx/sites-available/oaza-dla-autyzmu <<'NGINX_EOF'
server {
    listen 80;
    listen [::]:80;
    server_name _;
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
NGINX_EOF

# Aktywacja konfiguracji
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/oaza-dla-autyzmu /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx

# ==========================================
# KROK 19: Supervisor (queue workers)
# ==========================================
step "Instalacja Supervisor..."
apt install -y supervisor

cat > /etc/supervisor/conf.d/oaza-worker.conf <<'SUPERVISOR_EOF'
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
SUPERVISOR_EOF

supervisorctl reread
supervisorctl update
supervisorctl start oaza-worker:*

# ==========================================
# KROK 20: Cron (Laravel scheduler)
# ==========================================
step "Konfiguracja cron..."
(crontab -u deploy -l 2>/dev/null; echo "* * * * * cd /var/www/oaza-dla-autyzmu && php artisan schedule:run >> /dev/null 2>&1") | crontab -u deploy -

# ==========================================
# KROK 21: Firewall
# ==========================================
step "Konfiguracja firewall..."
ufw --force enable
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw status

# ==========================================
# KROK 22: Fail2Ban
# ==========================================
step "Instalacja Fail2Ban..."
apt install -y fail2ban
systemctl enable fail2ban
systemctl start fail2ban

# ==========================================
# PODSUMOWANIE
# ==========================================
echo ""
echo "=========================================="
echo -e "${GREEN}✅ DEPLOYMENT ZAKOŃCZONY POMYŚLNIE!${NC}"
echo "=========================================="
echo ""
echo "📋 Informacje:"
echo "  - Aplikacja: /var/www/oaza-dla-autyzmu"
echo "  - Baza danych: oaza_autyzmu"
echo "  - Użytkownik DB: oaza_user"
echo "  - Użytkownik systemu: deploy"
echo ""
echo "🌐 Dostęp:"
SERVER_IP=$(curl -s ifconfig.me)
echo "  - HTTP: http://$SERVER_IP"
if [ -n "$DOMAIN" ]; then
    echo "  - Domena: http://$DOMAIN"
fi
echo ""
echo "👤 Konto admin:"
echo "  - Email: $ADMIN_EMAIL"
echo "  - Hasło: [wpisane podczas instalacji]"
echo ""
echo "📝 Następne kroki:"
echo "  1. Ustaw A record domeny na IP: $SERVER_IP"
echo "  2. Zainstaluj SSL: sudo certbot --nginx -d $DOMAIN"
echo "  3. Zaloguj się jako admin i zweryfikuj placówki"
echo ""
echo "📂 Logi:"
echo "  - Laravel: tail -f /var/www/oaza-dla-autyzmu/storage/logs/laravel.log"
echo "  - Nginx: tail -f /var/log/nginx/error.log"
echo "  - Worker: tail -f /var/www/oaza-dla-autyzmu/storage/logs/worker.log"
echo ""
echo "=========================================="

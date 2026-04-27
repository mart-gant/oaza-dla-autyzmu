# Laravel Forge + Resend

Ten projekt ma już zainstalowany pakiet `resend/resend-laravel` i skonfigurowany transport `resend` w Laravelu. Na Forge nie potrzebujesz SMTP, tylko poprawnych zmiennych środowiskowych i adresu nadawcy zweryfikowanego w Resend.

## 1. Zmienne środowiskowe w Forge

W panelu strony w Laravel Forge ustaw co najmniej:

```env
MAIL_MAILER=resend
RESEND_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=no-reply@twoja-domena.pl
MAIL_FROM_NAME="Oaza dla Autyzmu"
MAIL_TO_ADDRESS=kontakt@twoja-domena.pl
```

Wymagania:

- `RESEND_KEY` wygeneruj w panelu Resend.
- `MAIL_FROM_ADDRESS` musi być adresem z domeny zweryfikowanej w Resend.
- `MAIL_TO_ADDRESS` to adres, na który ma wpadać formularz kontaktowy.

## 2. Co Forge ma zrobić po deployu

Po zmianie `.env` albo po pierwszym deployu uruchom na serwerze:

```bash
php artisan optimize:clear
php artisan config:cache
```

Jeśli używasz skryptu deploymentu w Forge, dopilnuj, żeby po `git pull` i instalacji zależności wykonał przynajmniej:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jeśli Forge buduje frontend na serwerze, zostaw też standardowy krok Vite:

```bash
npm ci
npm run build
```

## 3. Szybki test na Forge

Po deployu możesz sprawdzić konfigurację:

```bash
php artisan tinker --execute="dump(config('mail.default')); dump(config('services.resend.key') !== null);"
```

Oczekiwany wynik:

- pierwszy `dump()` powinien zwrócić `"resend"`
- drugi `dump()` powinien zwrócić `true`

## 4. Jeśli mail dalej nie wychodzi

Najczęstsze przyczyny:

- `MAIL_FROM_ADDRESS` nie jest zweryfikowany w Resend
- w Forge zmieniłeś `.env`, ale nie wyczyściłeś cache konfiguracji
- wkleiłeś zły `RESEND_KEY`
- domena ma niepoprawne rekordy DNS wymagane przez Resend

Do szybkiej diagnostyki:

```bash
php artisan optimize:clear
tail -n 100 storage/logs/laravel.log
```

## 5. Jak to działa w tym projekcie

Formularz kontaktowy wysyła mail przez domyślny mailer Laravel, więc po ustawieniu `MAIL_MAILER=resend` nie trzeba zmieniać kodu kontrolera ani mailable. Wysyłka jest realizowana z poziomu [app/Http/Controllers/ContactController.php](/Users/marcingantkowski/Documents/oaza-dla-autyzmu/app/Http/Controllers/ContactController.php#L17).

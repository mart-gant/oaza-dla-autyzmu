#  Oaza dla Autyzmu

Platforma społecznościowa dedykowana osobom z autyzmem, ich rodzinom oraz specjalistom. Projekt umożliwia wymianę doświadczeń, dostęp do informacji o placówkach i specjalistach oraz edukację poprzez artykuły.

## Spis treści

- [O projekcie](#o-projekcie)
- [Funkcjonalności](#funkcjonalności)
- [Instalacja](#instalacja)
- [Konfiguracja](#konfiguracja)
- [Uruchomienie](#uruchomienie)
- [Struktura projektu](#struktura-projektu)
- [Technologie](#technologie)
- [Konta testowe](#konta-testowe)
- [Licencja](#licencja)

## O projekcie

**Oaza dla Autyzmu** to kompleksowa platforma webowa stworzona z myślą o społeczności związanej z autyzmem. Aplikacja oferuje:

-  **Forum dyskusyjne** - bezpieczne miejsce wymiany doświadczeń i wsparcia
- **Bazę placówek** - katalog ośrodków terapeutycznych i edukacyjnych
-  **Bazę specjalistów** - dostęp do profesjonalistów wspierających osoby z autyzmem
-  **Poradnik wiedzy** - artykuły edukacyjne i praktyczne porady
-  **System recenzji** - oceny i opinie o placówkach
-  **Profile użytkowników** - personalizacja doświadczeń

##  Funkcjonalności

### Dla użytkowników:
- Rejestracja i autoryzacja (Laravel Breeze)
- Zarządzanie profilem użytkownika
- Przeglądanie i wyszukiwanie placówek
- Przeglądanie i wyszukiwanie specjalistów
- Tworzenie i edycja artykułów
-  Udział w dyskusjach na forum
-  Dodawanie recenzji placówek
-  System reakcji (like/dislike)
- Śledzenie wizyt w placówkach
- Formularz kontaktowy

### Dla administratorów:
- Panel administracyjny
- Zarządzanie użytkownikami (role, zawieszenia, personifikacja)
-  Zarządzanie placówkami
-  Logi audytowe działań w systemie
-  Export danych

### Dla specjalistów:
- Dedykowane profile specjalistów
- Możliwość prezentacji specjalizacji
-  Kontakt z rodzinami

##  Instalacja

### Wymagania systemowe:
- **PHP** 8.2 lub nowszy
- **Composer** 2.x
- **Node.js** 18.x lub nowszy
- **NPM** lub **Yarn**
- **MySQL** 8.0+ lub **PostgreSQL** 14+
- **Laravel Herd** (opcjonalnie, dla łatwiejszego development)

### Krok 1: Klonowanie repozytorium

```bash
git clone https://github.com/twoj-uzytkownik/oaza-dla-autyzmu.git
cd oaza-dla-autyzmu
```

### Krok 2: Instalacja zależności

```bash
# Zależności PHP
composer install

# Zależności JavaScript
npm install
```

### Krok 3: Konfiguracja środowiska

```bash
# Kopiuj plik .env
cp .env.example .env

# Wygeneruj klucz aplikacji
php artisan key:generate
```

### Krok 4: Konfiguracja bazy danych

Edytuj plik `.env` i ustaw parametry bazy danych:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oaza_dla_autyzmu
DB_USERNAME=root
DB_PASSWORD=
```

### Krok 5: Migracja i dane testowe

```bash
# Uruchom migracje
php artisan migrate

# Wypełnij bazę danymi testowymi (opcjonalnie)
php artisan db:seed
```

### Krok 6: Budowanie assetów

```bash
# Dla produkcji:
npm run build

# Dla developmentu (z hot reload):
npm run dev
```

##  Konfiguracja

### Konfiguracja email (opcjonalnie)

W pliku `.env` ustaw parametry serwera SMTP:

```env
MAIL_MAILER=failover
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your-app-password-without-spaces
MAIL_ENCRYPTION=tls
MAIL_TIMEOUT=15
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
MAIL_TO_ADDRESS=kontakt@twoja-domena.pl
```

> Laravel Cloud: upewnij się, że wszystkie zmienne `MAIL_*` są ustawione w panelu środowiska (Production),
> a po zmianie konfiguracji wykonaj redeploy. Dla Gmail używaj **App Password bez spacji**.

### Konfiguracja storage

```bash
# Stwórz symboliczny link dla storage
php artisan storage:link
```

## 🎮 Uruchomienie

### Development (lokalnie):

```bash
# Uruchom serwer Laravel
php artisan serve

# W osobnym terminalu uruchom Vite (jeśli używasz npm run dev)
npm run dev
```

Aplikacja będzie dostępna pod adresem: **http://localhost:8000**

### Z Laravel Herd:

Jeśli używasz Laravel Herd, aplikacja automatycznie będzie dostępna pod:
**http://oaza-dla-autyzmu.test**

### Health Check:

Sprawdź status aplikacji: **http://localhost:8000/health**

##  Struktura projektu

```
oaza-dla-autyzmu/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Kontrolery
│   │   │   ├── Admin/         # Panel administracyjny
│   │   │   ├── Api/           # Endpointy API
│   │   │   └── Auth/          # Autoryzacja
│   │   └── Requests/          # Form requests
│   ├── Models/                # Modele Eloquent
│   ├── Notifications/         # Powiadomienia
│   └── Policies/              # Polityki autoryzacji
├── database/
│   ├── migrations/            # Migracje bazy danych
│   ├── seeders/               # Seedery
│   └── factories/             # Factory dla testów
├── resources/
│   ├── views/                 # Widoki Blade
│   │   ├── articles/          # Artykuły
│   │   ├── facilities/        # Placówki
│   │   ├── forum/             # Forum
│   │   ├── specialists/       # Specjaliści
│   │   └── admin/             # Panel admin
│   ├── css/                   # Style CSS
│   └── js/                    # JavaScript
├── routes/
│   ├── web.php                # Route'y webowe
│   └── auth.php               # Route'y autoryzacji
└── tests/                     # Testy automatyczne
```

## Technologie

### Backend:
- **Laravel 11** - Framework PHP
- **MySQL/PostgreSQL** - Baza danych
- **Laravel Sanctum** - Autoryzacja API
- **Laravel Breeze** - System autoryzacji

### Frontend:
- **Blade Templates** - Silnik szablonów
- **Tailwind CSS 4.0** - Framework CSS
- **Vite** - Build tool
- **Alpine.js** (opcjonalnie) - Interaktywność

### DevOps & Narzędzia:
- **Composer** - Zarządzanie zależnościami PHP
- **NPM** - Zarządzanie zależnościami JS
- **Laravel Herd** - Lokalne środowisko development

## 👤 Konta testowe

Po uruchomieniu seedera (`php artisan db:seed`) dostępne są przykładowe konta:

**Użytkownik testowy:**
- Email: `test@example.com`
- Hasło: `password`

**Administrator (jeśli utworzono):**
- Email: `admin@example.com`
- Hasło: `password`

## Główne endpointy

### Publiczne:
- `/` - Strona główna
- `/articles` - Poradnik wiedzy (artykuły)
- `/facilities` - Lista placówek
- `/specialists` - Lista specjalistów
- `/forum` - Forum dyskusyjne
- `/contact` - Formularz kontaktowy

### Chronione (wymagają logowania):
- `/dashboard` - Panel użytkownika
- `/profile` - Profil użytkownika
- `/articles/create` - Tworzenie artykułu
- `/my-visits` - Historia wizyt
- `/admin` - Panel administratora (tylko admin)

## Role użytkowników

System wspiera następujące role:

- **user** - Standardowy użytkownik
- **specialist** - Specjalista (terapeuta, lekarz)
- **moderator** - Moderator forum i treści
- **admin** - Administrator (pełny dostęp)

## Testowanie

```bash
# Uruchom testy PHPUnit
php artisan test

# Uruchom testy Pest (jeśli używane)
./vendor/bin/pest

# Z coverage
php artisan test --coverage
```

##  Kolejne kroki rozwoju

Planowane funkcjonalności:

- [ ] System wiadomości prywatnych
- [ ] Zaawansowane filtrowanie (lokalizacja, specjalizacja)
- [ ] Integracja z mapami Google
- [ ] Kalendarz wydarzeń i webinarów
- [ ] System zapisywania ulubionych
- [ ] Powiadomienia email
- [ ] API RESTful dla aplikacji mobilnej
- [ ] Galerie zdjęć dla placówek
- [ ] System raportowania nieprawidłowości

## Współpraca

Projekt jest otwarty na współpracę! Jeśli chcesz pomóc w rozwoju:

1. Forkuj repozytorium
2. Stwórz branch dla swojej funkcjonalności (`git checkout -b feature/AmazingFeature`)
3. Commituj zmiany (`git commit -m 'Add some AmazingFeature'`)
4. Push do brancha (`git push origin feature/AmazingFeature`)
5. Otwórz Pull Request

## Licencja

Projekt wykorzystuje framework Laravel, który jest dostępny na licencji [MIT](https://opensource.org/licenses/MIT).

##  Kontakt

W razie pytań lub problemów, skorzystaj z formularza kontaktowego w aplikacji lub otwórz Issue na GitHubie.

---

**Zbudowano z dla społeczności osób z autyzmem**

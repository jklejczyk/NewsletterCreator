# Newsletter Creator

![Tests](https://github.com/jklejczyk/NewsletterCreator/actions/workflows/backend-tests.yaml/badge.svg)
![Pint](https://github.com/jklejczyk/NewsletterCreator/actions/workflows/backend-pint.yaml/badge.svg)
![Security](https://github.com/jklejczyk/NewsletterCreator/actions/workflows/backend-security.yaml/badge.svg)
[![codecov](https://codecov.io/gh/jklejczyk/NewsletterCreator/graph/badge.svg)](https://codecov.io/gh/jklejczyk/NewsletterCreator)
![PHPStan](https://img.shields.io/badge/PHPStan-level%207-brightgreen)

System agregujący treści z zewnętrznych źródeł (RSS, NewsAPI), przetwarzający je przez AI (OpenAI) i wysyłający spersonalizowane newslettery do subskrybentów. Full-stack: Laravel 13 + Vue 3.

[Dokumentacja API (Scramble)](https://jklejczyk.github.io/NewsletterCreator/)

## Tech stack

**Backend:** PHP 8.3, Laravel 13, PostgreSQL 17, Redis 7, Pest, PHPStan (level 7), Laravel Pint

**Frontend:** Vue 3, TypeScript, Vite, Pinia, vue-router

**Infrastruktura:** Docker Compose, GitHub Actions (CI/CD)

## Architektura

Projekt oparty o **CQRS** (Command Query Responsibility Segregation) z **Domain Events**.

- **Command side** — operacje zapisu przechodzą przez `CommandBus`: kontroler tworzy Command DTO, bus rozwiązuje handler po nazwie klasy, handler wykonuje logikę i emituje eventy.
- **Query side** — operacje odczytu przechodzą przez `QueryBus`: kontroler tworzy Query DTO z filtrami, bus rozwiązuje handler, handler zwraca dane.
- **Domain Events** — handlery emitują eventy (`SubscriberRegistered`, `NewsletterCreated`), listenery reagują (dispatch jobów na kolejkę Redis). Rejestracja eventów w `AppServiceProvider::boot()`.
- **API Resources** — warstwa transformacji między modelami Eloquent a odpowiedziami JSON.

```
app/
├── Domain/                  # Logika biznesowa dla określonego kontekstu
│   ├── Article/
│   │   ├── Commands/        # ImportArticles, ProcessArticle
│   │   ├── Queries/         # GetArticlesQuery + Handler
│   │   ├── Events/          # ArticleImported
│   │   ├── Listeners/       # ProcessArticleListener
│   │   ├── Models/          # Article
│   │   └── Clients/         # NewsApiClient, RssFeedIoClient
│   └── Newsletter/
│       ├── Commands/        # Subscribe, ConfirmSubscription, Unsubscribe, CreateNewsletter
│       ├── Queries/         # GetNewslettersQuery + Handler
│       ├── Events/          # SubscriberRegistered, NewsletterCreated
│       ├── Listeners/       # SendSubscriptionConfirmation, SendNewsletter
│       ├── Exceptions/      # ConfirmationTokenExpired, ConfirmationTokenInvalid
│       └── Models/          # Subscriber, Newsletter, NewsletterSend
├── Http/
│   ├── Controllers/Api/V1/  # Cienkie kontrolery: walidacja + dispatch
│   ├── Requests/Api/V1/     # FormRequests z walidacją
│   └── Resources/           # ArticleResource, NewsletterResource, ...
├── Infrastructure/
│   └── Bus/                 # CommandBus (void), QueryBus (returns data)
├── Jobs/                    # Kolejkowane: SendSubscriptionConfirmation, SendPersonalizedNewsletter
├── Mail/                    # Mailables: SubscriptionConfirmation, PersonalizedNewsletter
└── Services/                # OpenAiService (summary + kategoryzacja)
```

## API Endpoints

### Subskrybenci (publiczne)

| Metoda | Endpoint | Opis |
|--------|----------|------|
| POST | `/api/v1/subscribers` | Rejestracja z double opt-in |
| GET | `/api/v1/subscribers/confirm/{token}` | Potwierdzenie emaila |
| DELETE | `/api/v1/subscribers/{id}` | Wypisanie z newslettera |

### Artykuły (publiczne)

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/api/v1/articles` | Lista z filtrowaniem (`?category`, `?date_from`, `?date_to`) |
| GET | `/api/v1/articles/{id}` | Szczegóły artykułu |

### Newslettery (auth: Sanctum)

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/api/v1/newsletters` | Historia z filtrowaniem (`?status`, `?date_from`, `?date_to`) |
| GET | `/api/v1/newsletters/{id}/stats` | Statystyki wysyłki z listą odbiorców |
| POST | `/api/v1/newsletters/send` | Ręczne triggerowanie wysyłki |

## Główne flow

### Import i przetwarzanie artykułów
```
Scheduler (co godzine) → articles:import
  → ImportArticlesJob (kolejka Redis)
    → ImportArticlesCommandHandler (pobiera z RSS/NewsAPI)
      → ArticleImported event
        → ProcessArticleListener → ProcessArticleJob
          → OpenAI: summary + kategoryzacja
```

### Rejestracja subskrybenta (double opt-in)
```
POST /subscribers → SubscribeCommandHandler
  → tworzy Subscriber (is_active=false, token)
  → SubscriberRegistered event
    → SendSubscriptionConfirmationListener → Job → Mail

GET /subscribers/confirm/{token} → ConfirmSubscriptionCommandHandler
  → weryfikuje token + TTL (48h konfigurowalny)
  → is_active=true, confirmed_at=now
```

### Wysyłka newslettera
```
Scheduler (codziennie 8:00) / POST /newsletters/send
  → CreateNewsletterCommandHandler
    → zbiera przetworzone artykuły z ostatnich 24h
    → tworzy Newsletter (status=DRAFT)
    → NewsletterCreated event
      → SendNewsletterListener
        → per aktywny subskrybent: SendPersonalizedNewsletterJob
          → filtruje artykuły wg preferencji subskrybenta
          → renderuje PersonalizedNewsletterMail
          → tworzy NewsletterSend record
```

## Uruchomienie

```bash
# 1. Sklonuj repo
git clone git@github.com:jklejczyk/NewsletterCreator.git
cd NewsletterCreator

# 2. Przygotuj plik środowiskowy dla Dockera
cp backend/.env.docker.example backend/.env.docker
# Edytuj backend/.env.docker — ustaw klucze API (OPENAI, NEWSAPI)

# 3. Wystartuj stack
docker compose up -d

# 4. Zainstaluj zależności PHP (jeśli vendor pusty)
docker compose run --rm backend composer install

# 5. Uruchom migracje
docker compose exec backend php artisan migrate

# 6. (Opcjonalnie) Zaseeduj admina
docker compose exec backend php artisan db:seed --class=AdminSeeder
# Wyświetli token Sanctum do używania z endpointami /newsletters/*
```

Aplikacja dostępna pod:
- **Backend API:** http://localhost:8082/api/v1
- **Frontend:** http://localhost:5173

## Admin i autoryzacja

Endpointy `/newsletters/*` wymagają tokenu Sanctum. Aby go uzyskać:

```bash
docker compose exec backend php artisan db:seed --class=AdminSeeder
# Output: Admin token: 1|abc123...
```

Użycie tokenu:

```bash
curl -H "Authorization: Bearer 1|abc123..." http://localhost:8082/api/v1/newsletters
```

## Komendy Artisan

### Scheduler (automatyczne)

| Komenda | Harmonogram | Opis |
|---------|-------------|------|
| `articles:import` | Co godzinę | Pobiera artykuły z RSS i NewsAPI, kolejkuje przetwarzanie AI |
| `newsletter:send` | Codziennie o 8:00 | Tworzy newsletter z artykułów z ostatnich 24h i wysyła do subskrybentów |

### Ręczne triggerowanie

```bash
# Import artykułów (odpala ImportArticlesJob na kolejkę)
docker compose exec backend php artisan articles:import

# Wysyłka newslettera (tworzy newsletter + dispatch jobów per subskrybent)
docker compose exec backend php artisan newsletter:send

# Uruchomienie schedulera w trybie dev (odpalą komendy wg harmonogramu)
docker compose exec backend php artisan schedule:work

# Podgląd kolejki (przetwarzane joby)
docker compose exec backend php artisan queue:listen
```

## Testy

```bash
cd backend

# Wszystkie testy
php artisan test

# Pojedynczy plik
./vendor/bin/pest tests/Feature/Domain/Newsletter/Commands/SubscribeCommandHandlerTest.php

# Filtrowanie po nazwie
php artisan test --filter=SubscriberController

# PHPStan
./vendor/bin/phpstan analyse

# Formatter
./vendor/bin/pint
```

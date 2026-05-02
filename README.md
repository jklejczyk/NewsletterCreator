# Newsletter Creator

![Tests](https://github.com/jklejczyk/NewsletterCreator/actions/workflows/backend-tests.yaml/badge.svg)
![Pint](https://github.com/jklejczyk/NewsletterCreator/actions/workflows/backend-pint.yaml/badge.svg)
![Security](https://github.com/jklejczyk/NewsletterCreator/actions/workflows/backend-security.yaml/badge.svg)
[![codecov](https://codecov.io/gh/jklejczyk/NewsletterCreator/graph/badge.svg)](https://codecov.io/gh/jklejczyk/NewsletterCreator)
![PHPStan](https://img.shields.io/badge/PHPStan-level%207-brightgreen)

System full-stack (Laravel 13 + Vue 3) agregujący treści z zewnętrznych źródeł (RSS, NewsAPI), przetwarzający je przez AI (OpenAI) i wysyłający spersonalizowane newslettery do subskrybentów. Zawiera publiczny landing z subskrypcją oraz panel administratora do zarządzania wysyłkami.

[Dokumentacja API (Scramble)](https://jklejczyk.github.io/NewsletterCreator/)

## Tech stack

**Backend:** PHP 8.3, Laravel 13, PostgreSQL 17, Redis 7, Pest, PHPStan (level 7), Laravel Pint

**Frontend:** Vue 3, TypeScript, Vite, Pinia, vue-router, Tailwind CSS 4, shadcn-vue, axios

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

## Architektura frontu

Frontend zorganizowany w **feature-based structure** mirrorującą bounded contexts backendu, z warstwą `shared/` na cross-cutting concerns.

- **Type-safe HTTP layer** — axios instance w `shared/api/client.ts` z interceptorami (Bearer token, mapowanie błędów). Każde API zwraca typowane odpowiedzi przez generyki `Paginated<T>`, `Resource<T>`.
- **Custom error classes** — `ValidationError` (422 z Laravel) i `ApiError` (inne 4xx/5xx + network). Feature code nigdy nie widzi `AxiosError`.
- **Pinia setup-stores per feature** — request lifecycle z 4-stanowym state machine (`idle`/`loading`/`success`/`error`). Multiple operation slots dla feature'ów z wieloma akcjami (newsletters: list + detail + send).
- **Composables jako reusable logic** — `useArticleFilters`/`useNewsletterFilters` (URL-as-truth dla filtrów + paginacji), `useDebouncedRef` (input debouncing), `useSubscribeForm` (form lifecycle z 422 mapping).
- **URL-as-state** — filtry list (kategorie/status, daty, paginacja) żyją w URL'u. Refresh, browser back, deep linking i share działają natywnie.
- **Layouts as nested routes** — `PublicLayout`/`AdminLayout` jako parent route'y, feature views jako children.

```
src/
├── App.vue
├── main.ts
├── router.ts                   # composes routes from features
├── layouts/                    # PublicLayout, AdminLayout
├── shared/
│   ├── api/                    # axios client + typed errors
│   ├── components/             # shadcn-vue primitives
│   ├── composables/            # useDebouncedRef
│   ├── types/                  # Paginated<T>, Resource<T>, RequestStatus
│   └── utils/                  # formatDate, cn
└── features/
    ├── articles/
    │   ├── views/              # ArticleListView
    │   ├── components/         # ArticleCard, CategoryBadge, ArticleFilters
    │   ├── composables/        # useArticleFilters
    │   ├── store.ts            # Pinia setup-store
    │   ├── api.ts              # fetchArticles, fetchArticle
    │   ├── types.ts            # Article, ArticleFilters, ArticleCategory
    │   ├── categories.ts       # CATEGORY_META (label + colors)
    │   └── routes.ts           # exports RouteRecordRaw[]
    ├── subscribers/
    │   ├── views/              # SubscribeView, ConfirmView, UnsubscribeView
    │   ├── components/         # SubscribeForm, PreferencesPicker
    │   ├── composables/        # useSubscribeForm
    │   ├── store.ts, api.ts, types.ts, routes.ts
    └── newsletters/
        ├── views/              # NewsletterListView, NewsletterDetailView
        ├── components/         # NewsletterRow, StatusBadge
        ├── composables/        # useNewsletterFilters
        ├── store.ts, api.ts, types.ts, statuses.ts, routes.ts
```

## Funkcjonalności (UI)

### Publiczna część (`/`, `/subscribe`, `/confirm/:token`, `/unsubscribe/:id`)

- **Lista artykułów** z filtrowaniem (kategoria, daty), paginacją i URL sync
- **Skeleton loadery**, retry buttony, filter-aware empty states
- **Formularz subskrypcji** z walidacją Laravel 422 mapowaną na konkretne pola
- **Confirm flow** — kliknięcie linka z maila aktywuje konto (+ idempotentny "już potwierdzone" przy drugim kliku)
- **Unsubscribe** — single-action page z sygnalizacją sukcesu/błędu

### Panel admina (`/admin/newsletters`, `/admin/newsletters/:id`)

- **Lista newsletterów** z filtrowaniem (status, daty), paginacją i URL sync
- **Szczegóły newslettera** z metadanymi i tabelą wysyłek per subskrybent
- **Manualne triggerowanie wysyłki** ("Wyślij newsletter" → POST → 202 → refresh listy)
- **Auth** — Bearer token (Sanctum) wstrzykiwany przez axios interceptor

### UX features

- **Mobile-first responsive** (Tailwind utilities)
- **Polskojęzyczne komunikaty błędów** — backend serwuje user-friendly messages, frontend preferuje server's message z fallbackiem
- **Type-safe routing** — named routes z TS-em narrowing param'ów

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

# 7. Skonfiguruj frontend env
cp frontend/.env.example frontend/.env
# Wklej token z punktu 6 (lub wygenerowany przez tinker) do VITE_ADMIN_TOKEN

# 8. Restart frontu po edycji env (Vite czyta env przy starcie)
docker compose restart frontend
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

## Frontend dev commands

```bash
# Vite dev server (już biegnie z docker compose up)
docker compose exec frontend npm run dev

# Production build z type-check
docker compose exec frontend npm run build

# Tylko type-check (vue-tsc --build)
docker compose exec frontend npm run type-check

# Prettier
docker compose exec frontend npm run format

# Dodawanie pakietów — w obu miejscach (kontener dla runtime, host dla IDE intellisense)
docker compose exec frontend npm install <pakiet>
cd frontend && npm install <pakiet>

# Dodawanie kolejnych shadcn-vue komponentów
docker compose exec frontend npx shadcn-vue@latest add <komponent>
```

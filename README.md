# Inventory & Reservation — Coding Challenge

Gestione inventario aziendale con richieste utenti e approvazioni admin.  
Stack: **Laravel 12**, **Breeze (Inertia + Vue 3)**, **Vite**, **SQLite**.  
Include: autenticazione, ruoli, CRUD inventario, richieste *inventory* / *to-buy*, controllo disponibilità per periodo, dashboard KPI, command CLI, seed demo, test unit/feature.

---

## Table of contents
- [Prerequisiti](#prerequisiti)
- [Setup veloce (5 step)](#setup-veloce-5-step)
- [Credenziali demo](#credenziali-demo)
- [Script utili](#script-utili)
- [Cosa fa l’app (overview funzionale)](#cosa-fa-lapp-overview-funzionale)
- [Modello dati](#modello-dati)
- [Regole di business: disponibilità](#regole-di-business-disponibilità)
- [Rotte principali](#rotte-principali)
- [Ruoli e permessi](#ruoli-e-permessi)
- [Seeder & dati demo](#seeder--dati-demo)
- [Test](#test)
- [Configurazione (.env) — SQLite di default](#configurazione-env--sqlite-di-default)


---

## Prerequisiti
- PHP **8.2+**
- Composer **2+**
- Node **18+** (o 20+) e npm
- Estensione **pdo_sqlite** abilitata (per DB di default)

---

## Setup veloce (5 step)

```bash
# 1) dipendenze
composer install
npm install

# 2) env + app key
cp .env.example .env
php artisan key:generate

# 3) DB SQLite (default del progetto)
mkdir -p database
touch database/database.sqlite

# 4) migrazioni + seed demo
php artisan migrate --seed

# 5) avviare backend e frontend
php artisan serve
npm run dev
```

- Backend: http://127.0.0.1:8000
- Frontend (Vite dev server): http://127.0.0.1:5173 (gestito automaticamente da Breeze/Vite)

⸻

## Credenziali demo
- Admin: admin@company.com / password
- User demo: john.doe@company.com / password
- User demo: jane.smith@company.com / password
(+ altri utenti generati con password password)

## Script utili

``` bash
# sviluppo
npm run dev          # Vite con HMR
php artisan serve    # server PHP locale

# dati demo
php artisan migrate:fresh --seed

# test
php artisan test
php artisan test --parallel

# build
npm run build
```

## Cosa fa l’app (overview funzionale)
- Autenticazione (Laravel Breeze: login, registrazione, profilo).
- Ruoli: admin e user.
- Catalogo (user): lista Items disponibili, filtri & ricerca (nome/categoria/stato), pulsante Richiedi che porta al form con l’item pre-selezionato.
- Richieste utente (user):
- Inventory: item, quantità, periodo (data inizio/fine).
- To-buy: richiesta di acquisto (solo note + qty, nessun item/periodo).
- Pagina Le mie richieste con stato pending/approved/rejected.
- Pagina Le mie prenotazioni (se approvate, vedi la reservation).
- Inventario (admin):
- CRUD Items (create, edit inline, delete).
- Categorie collegate.
- Approvals (admin):
- Lista Richieste utenti, approva/rifiuta.
- In approvazione, per inventory si crea una Reservation se c’è stock per quel periodo.
- Dashboard Admin: KPI (totali, richieste pending), top item richiesto, utente più attivo, filtro periodo.
- Comando CLI: items:create per inserire rapidamente un item.
- Flash messages UI per success/error.

### Modello dati
- users: name, email, password, role (admin/user)
- categories: name
- items: name, category_id, quantity, status (available/unavailable), description
- item_requests: user_id, type (inventory/to-buy), item_id?, quantity, start_date?, end_date?, note?, status (pending/approved/rejected)
- reservations: item_request_id, item_id, start_date, end_date, quantity

### Relazioni chiave:
- Item belongsTo Category
- ItemRequest belongsTo User e optionally belongsTo Item
- Reservation belongsTo ItemRequest e Item

Indici principali su reservations: item_id, start_date, end_date (per query su periodi).

### Regole di business: disponibilità

Al momento dell’approvazione di una richiesta inventory:
- Calcoliamo la disponibilità di un Item nel periodo richiesto sottraendo la somma delle Reservation esistenti che si sovrappongono alle date.
- Se available >= requested_quantity → approve & create reservation; altrimenti reject.

Logica implementata in App\Services\AvailabilityService (testata con unit test).

## Rotte principali

```bash
GET  /                  → redirect a /login o /dashboard
GET  /dashboard         → dashboard utente/admin (Inertia)

# Area utente (auth)
GET  /requests          → mie richieste
GET  /requests/create   → nuova richiesta (inventory/to-buy)
POST /requests          → salva richiesta
GET  /reservations      → mie prenotazioni

# Area admin (auth + can:admin)
GET    /admin/items           → inventario (lista + create + edit inline + delete)
POST   /admin/items
PUT    /admin/items/{id}
DELETE /admin/items/{id}

GET  /admin/requests          → richieste utenti
POST /admin/requests/{id}/approve
POST /admin/requests/{id}/reject
```

## Ruoli e permessi
- role nel modello User.
- Gate admin (policy semplice) → middleware can:admin per /admin/*.
- Navbar e dashboard cambiano dinamicamente in base al ruolo (voci admin visibili solo a admin).

## Seeder & dati demo

I seed creano:
- Admin fisso (admin@company.com/password),
- 2 utenti fissi (john.doe@company.com, jane.smith@company.com, password password),
- Categorie (Laptops, Monitors, Smartphones, Peripherals, Accessories),
- Items realistici (es. MacBook Pro 16”, Dell XPS 13, LG UltraWide 34”, iPhone 16, Logitech MX Keys + extra),
- Richieste utente eterogenee (inventory/to-buy). ~60% delle inventory vengono approvate creando reservation se c’è disponibilità.

## Comandi:

```bash
php artisan migrate:fresh --seed        # reset + seed completo
php artisan db:seed --class=DemoSeeder  # solo seed
```

## Creazione rapida di un item:

```bash
php artisan items:create \
  --name="MacBook Pro 14\"" \
  --category="Laptops" \
  --quantity=3 \
  --status=available \
  --description="Notebook per sviluppo"
```

## Opzioni:
- --name (obbl.)
- --category (ID o nome; se nome non esiste, viene creato)
- --quantity (default: 1)
- --status (available|unavailable, default: available)
- --description (opzionale)

## Aiuto:

```bash
php artisan items:create -h
```

## Test

Suite unit + feature/integration:
- tests/Unit/AvailabilityServiceTest.php
- calcolo disponibilità: no overlap, overlap, mai negativo.
- tests/Feature/RequestApprovalFlowTest.php
- user crea richiesta inventory → admin approva → reservation creata, status approved.
- tests/Feature/ToBuyRequestTest.php
- richiesta to-buy salvata, visibile in “Le mie richieste”, nessuna reservation.
- tests/Feature/ItemsCreateCommandTest.php
- verifica comando items:create.

## Esecuzione:

```bash
php artisan test
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

## Configurazione (.env) — SQLite di default

.env minimal per sviluppo SQLite:

```bash
APP_NAME="Inventory"
APP_ENV=local
APP_KEY=base64:... (generato da key:generate)
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
```

## Per MySQL, sostituisci:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory
DB_USERNAME=root
DB_PASSWORD=
```

# Autore: Marco De Michele
# Tech: Laravel 12, Breeze (Inertia + Vue 3), Vite, SQLite.
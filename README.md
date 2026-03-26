# Laravel + Vue 3 + Inertia.js — Client Management System

A full-stack client management application built with:

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Vue 3 (Composition API), Inertia.js |
| Styling | TailwindCSS v4 |
| Database | MySQL 8.4 |
| Environment | Docker via Laravel Sail |
| Build tool | Vite 7 |

---

## Requirements

Before you begin, make sure you have the following installed on your machine:

- **Docker Desktop** — [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)
- **PHP 8.2+** — needed once to bootstrap Composer before Sail takes over
- **Composer** — [https://getcomposer.org](https://getcomposer.org)

> After the first install, **everything runs through Sail** (no local PHP/Node needed day-to-day).

---

## Installation

### Option A — Automated (recommended)

Clone the repo and run the install script:

```bash
git clone https://github.com/kujtimkrasniqi/laravel-vue-inertia-app.git
cd laravel-vue-inertia-app

bash install.sh
```

The script will:
1. Copy `.env.example` → `.env`
2. Install Composer dependencies (local PHP, one time only)
3. Generate `APP_KEY`
4. Build the Docker image
5. Start containers (`sail up -d`)
6. Wait for MySQL to be ready
7. Run migrations + seed 14 sample clients
8. Install Node.js dependencies inside the container
9. Build frontend assets (Vite + Tailwind)

**Done** — open [http://localhost](http://localhost).

---

### Option B — Manual (step by step)

```bash
# 1. Clone
git clone https://github.com/kujtimkrasniqi/laravel-vue-inertia-app.git
cd laravel-vue-inertia-app

# 2. Environment
cp .env.example .env

# 3. Install PHP dependencies (local PHP required here only)
composer install

# 4. Generate app key
php artisan key:generate

# 5. Build & start Docker containers
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

# 6. Run migrations + seed sample data
./vendor/bin/sail artisan migrate --seed

# 7. Install JS dependencies & build assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Open [http://localhost](http://localhost).

---

## Daily Development

```bash
# Start containers
./vendor/bin/sail up -d

# Hot-reload dev server (Vite)
./vendor/bin/sail npm run dev

# Stop containers
./vendor/bin/sail down
```

---

## Makefile shortcuts

A `Makefile` is included for convenience. Run `make help` to see all commands.

```
make install        First-time setup (runs install.sh)

make up             Start containers
make down           Stop containers
make restart        Restart containers
make logs           Tail container logs
make shell          Open shell inside app container

make dev            Start Vite dev server (hot reload)
make build-assets   Build for production

make migrate        Run pending migrations
make fresh          migrate:fresh --seed (full DB reset)
make seed           Run seeders only
make tinker         Open Laravel Tinker
make routes         List all routes
make cache-clear    Clear all caches
make test           Run test suite
```

---

## Features

| Feature | Detail |
|---|---|
| Client CRUD | Create, edit, delete clients |
| Auto expiry | `expiry_date` = `start_date + 1 month` (automatic) |
| Mark as Paid | Extends `expiry_date` by +1 month from current expiry |
| Status | Active / Expired (computed, never stored) |
| Filters | All, Active, Expired, This Week, This Month |
| Dashboard | Live stats cards + recent clients table |
| Excel Export | Download clients as `.xlsx` (respects active filter) |
| Sample Data | 14 pre-seeded clients across all status groups |

---

## Project Structure

```
app/
├── Exports/ClientsExport.php         Excel export (maatwebsite/excel)
├── Http/
│   ├── Controllers/
│   │   ├── ClientController.php      CRUD + markAsPaid + export
│   │   └── DashboardController.php   Dashboard stats + recent
│   └── Requests/
│       ├── StoreClientRequest.php    Validation
│       └── UpdateClientRequest.php   Validation
├── Models/Client.php                 Domain model + accessors
├── Observers/ClientObserver.php      Auto-set expiry_date on create
├── Providers/AppServiceProvider.php  Register observer
└── Services/ClientQueryService.php   Shared filter/stats/format logic

database/
├── migrations/..._create_clients_table.php
└── seeders/ClientSeeder.php          14 sample clients

resources/js/
├── app.js                            Inertia + Ziggy bootstrap
├── Layouts/AppLayout.vue             Navbar, flash messages
├── Components/
│   ├── DashboardCards.vue            Stats cards
│   ├── FilterBar.vue                 Filter tabs with counts
│   ├── ClientTable.vue               Table + delete confirm + actions
│   ├── ClientForm.vue                Create/edit slide-up modal
│   └── StatusBadge.vue               Active/Expired badge
└── Pages/
    ├── Dashboard.vue                 Overview page
    └── Clients/Index.vue             Full client management page
```

---

## Docker Services

| Service | Port | Details |
|---|---|---|
| App (PHP 8.2) | `80` | Laravel + Vite served via Sail |
| MySQL 8.4 | `3306` | `sail:password@localhost/laravel` |
| Vite HMR | `5173` | Hot-module reload in dev mode |

---

## Environment Variables

Key variables in `.env` (see `.env.example` for the full list):

```env
APP_NAME=MyApp
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql          # ← Docker service name, not localhost
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

---

## Useful Sail Commands

```bash
./vendor/bin/sail artisan migrate:status
./vendor/bin/sail artisan route:list
./vendor/bin/sail artisan db:seed --class=ClientSeeder
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail composer require <package>
./vendor/bin/sail npm install <package>
./vendor/bin/sail tinker
```

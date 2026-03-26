#!/usr/bin/env bash
# =============================================================================
# install.sh — First-time project setup
#
# Requires on the HOST machine only:
#   - Docker + Docker Compose  (https://docs.docker.com/get-docker/)
#
# composer.json includes "platform": {"ext-gd": "1.0"} so Composer resolves
# packages correctly on any host, even without ext-gd installed locally.
# The actual ext-gd is present in the Sail container at runtime.
#
# After this script runs, everything else is done via:
#   ./vendor/bin/sail <command>
# =============================================================================

set -euo pipefail

CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

step() { echo -e "\n${CYAN}▶ $1${NC}"; }
ok()   { echo -e "${GREEN}✔ $1${NC}"; }
warn() { echo -e "${YELLOW}⚠ $1${NC}"; }
fail() { echo -e "${RED}✘ $1${NC}"; exit 1; }

echo -e "${CYAN}"
echo "  ██████╗ ███████╗████████╗██╗   ██╗██████╗ "
echo "  ██╔═══╝ ██╔════╝╚══██╔══╝██║   ██║██╔══██╗"
echo "  ███████╗█████╗     ██║   ██║   ██║██████╔╝"
echo "  ╚════██║██╔══╝     ██║   ██║   ██║██╔═══╝ "
echo "  ██████╔╝███████╗   ██║   ╚██████╔╝██║     "
echo "  ╚═════╝ ╚══════╝   ╚═╝    ╚═════╝ ╚═╝     "
echo -e "${NC}"
echo "  Laravel + Vue 3 + Inertia.js — First-time install"
echo ""

# ── 1. Check Docker ───────────────────────────────────────────────────────────
step "Checking Docker..."
if ! docker info > /dev/null 2>&1; then
    fail "Docker is not running. Start Docker Desktop and try again."
fi
ok "Docker is running"

# ── 2. Find PHP binary ────────────────────────────────────────────────────────
step "Locating PHP binary..."
PHP_BIN=""
for candidate in php php8.4 php8.3 php8.2 php8.1; do
    if command -v "$candidate" > /dev/null 2>&1; then
        PHP_BIN="$candidate"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    warn "PHP not found on host — falling back to Docker for Composer bootstrap."
    PHP_BIN=""
else
    PHP_VERSION=$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
    ok "PHP $PHP_VERSION found at $(command -v "$PHP_BIN")"
fi

# ── 3. Find Composer binary ───────────────────────────────────────────────────
# COMPOSER_CMD holds the full command to run Composer, e.g.:
#   "composer"               — system-installed executable (most common)
#   "php /path/to/composer.phar" — manual .phar install
COMPOSER_CMD=""
if [ -n "$PHP_BIN" ]; then
    # Check for an executable composer script first (installed via apt, brew, etc.)
    for candidate in composer /usr/local/bin/composer /usr/bin/composer "$HOME/.local/bin/composer"; do
        if command -v "$candidate" > /dev/null 2>&1; then
            COMPOSER_CMD="$candidate"
            break
        fi
    done
    # Fall back to running a .phar file directly with PHP
    if [ -z "$COMPOSER_CMD" ]; then
        for phar in composer.phar /usr/local/bin/composer.phar "$HOME/composer.phar"; do
            if [ -f "$phar" ]; then
                COMPOSER_CMD="$PHP_BIN $phar"
                break
            fi
        done
    fi
fi

# ── 4. Copy .env ──────────────────────────────────────────────────────────────
step "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    ok "Created .env from .env.example"
else
    warn ".env already exists — skipping copy"
fi

# ── 5. Install Composer dependencies ─────────────────────────────────────────
# composer.json has "platform": {"ext-gd": "1.0"} so Composer treats ext-gd
# as satisfied during dependency resolution on any host machine.
step "Installing PHP dependencies via Composer..."
if [ -n "$COMPOSER_CMD" ]; then
    echo "  Using host Composer: $COMPOSER_CMD"
    $COMPOSER_CMD install \
        --no-interaction --prefer-dist --optimize-autoloader --no-scripts
else
    echo "  No host Composer found — using Docker (laravelsail/php82-composer)."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install \
            --no-interaction --prefer-dist --optimize-autoloader --no-scripts
fi
ok "Composer dependencies installed"

# ── 6. Generate app key ───────────────────────────────────────────────────────
step "Generating application key..."
if [ -n "$PHP_BIN" ]; then
    "$PHP_BIN" artisan key:generate --ansi
else
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        php artisan key:generate --ansi
fi
ok "Application key generated"

# ── 7. Build Sail Docker image ────────────────────────────────────────────────
step "Building Docker containers (this may take a few minutes on first run)..."
./vendor/bin/sail build --no-cache
ok "Docker image built"

# ── 8. Start containers ───────────────────────────────────────────────────────
step "Starting containers in background..."
./vendor/bin/sail up -d
ok "Containers started"

# ── 9. Wait for MySQL to be ready ────────────────────────────────────────────
step "Waiting for MySQL to be ready..."
RETRIES=30
until ./vendor/bin/sail exec -T mysql mysqladmin ping --silent 2>/dev/null; do
    RETRIES=$((RETRIES - 1))
    if [ "$RETRIES" -eq 0 ]; then
        fail "MySQL did not become ready in time. Check: ./vendor/bin/sail logs mysql"
    fi
    echo -n "."
    sleep 2
done
echo ""
ok "MySQL is ready"

# ── 10. Run migrations + seed ─────────────────────────────────────────────────
step "Running migrations and seeding sample data..."
./vendor/bin/sail artisan migrate --seed --ansi
ok "Database migrated and seeded (14 sample clients created)"

# ── 11. Install Node dependencies ────────────────────────────────────────────
step "Installing Node.js dependencies..."
./vendor/bin/sail npm install
ok "Node.js dependencies installed"

# ── 12. Build frontend assets ─────────────────────────────────────────────────
step "Building frontend assets (Vite + TailwindCSS)..."
./vendor/bin/sail npm run build
ok "Frontend assets built"

# ── Done ──────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  ✅  Setup complete!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "  🌐  App URL:     ${CYAN}http://localhost${NC}"
echo -e "  🗄️   Database:   ${CYAN}mysql://sail:password@localhost:3306/laravel${NC}"
echo ""
echo -e "  Start dev server:  ${YELLOW}./vendor/bin/sail npm run dev${NC}"
echo -e "  Stop containers:   ${YELLOW}./vendor/bin/sail down${NC}"
echo -e "  All commands:      ${YELLOW}make help${NC}"
echo ""

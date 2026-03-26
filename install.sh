#!/usr/bin/env bash
# =============================================================================
# install.sh — First-time project setup (Docker-only, no host PHP required)
#
# Requires on the HOST machine only:
#   - Docker + Docker Compose  (https://docs.docker.com/get-docker/)
#
# Composer dependencies are installed INSIDE Docker, so ext-gd and all other
# PHP extensions are always available — no host PHP installation needed.
#
# After this script runs, everything is run via:
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
echo "  (No host PHP required — Composer runs inside Docker)"
echo ""

# ── 1. Check Docker ───────────────────────────────────────────────────────────
step "Checking Docker..."
if ! docker info > /dev/null 2>&1; then
    fail "Docker is not running. Start Docker Desktop and try again."
fi
ok "Docker is running"

# ── 2. Copy .env ──────────────────────────────────────────────────────────────
step "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    ok "Created .env from .env.example"
else
    warn ".env already exists — skipping copy"
fi

# ── 3. Install Composer dependencies via Docker (has all PHP extensions) ──────
step "Installing PHP dependencies via Docker composer image..."
echo "  (This avoids needing ext-gd or any PHP extension on your host machine)"
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
ok "Composer dependencies installed"

# ── 4. Generate app key via Docker ────────────────────────────────────────────
step "Generating application key..."
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    php artisan key:generate --ansi
ok "Application key generated"

# ── 5. Build Sail Docker image ────────────────────────────────────────────────
step "Building Docker containers (this may take a few minutes on first run)..."
./vendor/bin/sail build --no-cache
ok "Docker image built"

# ── 6. Start containers ───────────────────────────────────────────────────────
step "Starting containers in background..."
./vendor/bin/sail up -d
ok "Containers started"

# ── 7. Wait for MySQL to be ready ────────────────────────────────────────────
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

# ── 8. Run migrations + seed ─────────────────────────────────────────────────
step "Running migrations and seeding sample data..."
./vendor/bin/sail artisan migrate --seed --ansi
ok "Database migrated and seeded (14 sample clients created)"

# ── 9. Install Node dependencies ─────────────────────────────────────────────
step "Installing Node.js dependencies..."
./vendor/bin/sail npm install
ok "Node.js dependencies installed"

# ── 10. Build frontend assets ─────────────────────────────────────────────────
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

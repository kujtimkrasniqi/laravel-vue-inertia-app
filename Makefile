SAIL = ./vendor/bin/sail
.DEFAULT_GOAL := help

# =============================================================================
# SETUP
# =============================================================================

install: ## First-time setup (runs install.sh)
	@bash install.sh

# =============================================================================
# DOCKER
# =============================================================================

up: ## Start all containers in background
	$(SAIL) up -d

down: ## Stop and remove containers
	$(SAIL) down

restart: ## Restart all containers
	$(SAIL) down && $(SAIL) up -d

build: ## Rebuild Docker images from scratch
	$(SAIL) build --no-cache

logs: ## Tail container logs
	$(SAIL) logs -f

shell: ## Open a shell inside the app container
	$(SAIL) shell

# =============================================================================
# FRONTEND
# =============================================================================

dev: ## Start Vite dev server (hot reload)
	$(SAIL) npm run dev

build-assets: ## Build frontend assets for production
	$(SAIL) npm run build

npm-install: ## Install Node.js dependencies
	$(SAIL) npm install

# =============================================================================
# BACKEND
# =============================================================================

composer-install: ## Install PHP dependencies
	$(SAIL) composer install

composer-update: ## Update PHP dependencies
	$(SAIL) composer update

# =============================================================================
# DATABASE
# =============================================================================

migrate: ## Run all pending migrations
	$(SAIL) artisan migrate

migrate-fresh: ## Drop all tables and re-run all migrations
	$(SAIL) artisan migrate:fresh

seed: ## Run database seeders
	$(SAIL) artisan db:seed

fresh: ## Fresh migrate + seed (full reset)
	$(SAIL) artisan migrate:fresh --seed

rollback: ## Rollback the last migration batch
	$(SAIL) artisan migrate:rollback

migrate-status: ## Show migration status
	$(SAIL) artisan migrate:status

# =============================================================================
# ARTISAN
# =============================================================================

tinker: ## Open Laravel Tinker REPL
	$(SAIL) artisan tinker

routes: ## List all registered routes
	$(SAIL) artisan route:list

cache-clear: ## Clear all application caches
	$(SAIL) artisan optimize:clear

key-generate: ## Generate a new application key
	$(SAIL) artisan key:generate

# =============================================================================
# TESTING
# =============================================================================

test: ## Run the full PHPUnit test suite
	$(SAIL) artisan test

test-filter: ## Run a specific test (usage: make test-filter F=TestClassName)
	$(SAIL) artisan test --filter=$(F)

# =============================================================================
# HELP
# =============================================================================

help: ## Show this help message
	@echo ""
	@echo "  Laravel + Vue 3 + Inertia.js — Available commands"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""

.PHONY: install up down restart build logs shell dev build-assets npm-install \
        composer-install composer-update migrate migrate-fresh seed fresh \
        rollback migrate-status tinker routes cache-clear key-generate \
        test test-filter help

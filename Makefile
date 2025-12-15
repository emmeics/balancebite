# =============================================================================
# MAKEFILE - BalanceBite
# =============================================================================
# Comandi comuni per lo sviluppo.
# Uso: make <comando>
# Aiuto: make help
# =============================================================================

.PHONY: help install start stop restart logs shell test lint fix stan qa migrate seed cache jwt-keys

# Colori per output
GREEN  := \033[0;32m
YELLOW := \033[0;33m
BLUE   := \033[0;34m
NC     := \033[0m # No Color

# Variabili
DOCKER_COMPOSE = docker compose
SYMFONY = cd backend && php bin/console
COMPOSER = cd backend && composer

# Default
.DEFAULT_GOAL := help

# =============================================================================
# HELP
# =============================================================================

help: ## Mostra questo messaggio di aiuto
	@echo ""
	@echo "$(BLUE)BalanceBite$(NC) - Comandi disponibili"
	@echo ""
	@echo "$(YELLOW)Docker:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E '(start|stop|restart|logs)' | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Sviluppo:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E '(install|serve|shell|cache)' | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Database:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E '(migrate|seed|db-)' | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Quality:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E '(test|lint|fix|stan|qa)' | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(YELLOW)Setup:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | grep -E '(jwt-)' | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""

# =============================================================================
# DOCKER
# =============================================================================

start: ## Avvia i container Docker (PostgreSQL, Redis)
	@echo "$(GREEN)Avvio container...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Container avviati$(NC)"
	@echo ""
	@echo "PostgreSQL: localhost:5432"
	@echo "Redis:      localhost:6379"
	@echo ""

stop: ## Ferma i container Docker
	@echo "$(YELLOW)Fermo container...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✓ Container fermati$(NC)"

restart: stop start ## Riavvia i container Docker

logs: ## Mostra i log dei container (uso: make logs oppure make logs s=postgres)
	@if [ -z "$(s)" ]; then \
		$(DOCKER_COMPOSE) logs -f; \
	else \
		$(DOCKER_COMPOSE) logs -f $(s); \
	fi

# =============================================================================
# SVILUPPO
# =============================================================================

install: ## Installa le dipendenze del progetto
	@echo "$(GREEN)Installazione dipendenze...$(NC)"
	$(COMPOSER) install
	@echo "$(GREEN)✓ Dipendenze installate$(NC)"

serve: ## Avvia il server Symfony (sviluppo)
	@echo "$(GREEN)Avvio server Symfony...$(NC)"
	cd backend && symfony serve

shell: ## Apre una shell nel container PostgreSQL
	$(DOCKER_COMPOSE) exec postgres psql -U balancebite -d balancebite

cache: ## Pulisce la cache di Symfony
	@echo "$(GREEN)Pulizia cache...$(NC)"
	$(SYMFONY) cache:clear
	@echo "$(GREEN)✓ Cache pulita$(NC)"

# =============================================================================
# DATABASE
# =============================================================================

migrate: ## Esegue le migration del database
	@echo "$(GREEN)Esecuzione migration...$(NC)"
	$(SYMFONY) doctrine:migrations:migrate --no-interaction
	@echo "$(GREEN)✓ Migration completate$(NC)"

migrate-diff: ## Genera una nuova migration dalle modifiche alle Entity
	$(SYMFONY) doctrine:migrations:diff

migrate-status: ## Mostra lo stato delle migration
	$(SYMFONY) doctrine:migrations:status

db-create: ## Crea il database
	$(SYMFONY) doctrine:database:create --if-not-exists

db-drop: ## Elimina il database (ATTENZIONE!)
	$(SYMFONY) doctrine:database:drop --force --if-exists

db-reset: db-drop db-create migrate seed ## Reset completo del database

seed: ## Carica i dati di test (fixtures)
	@echo "$(GREEN)Caricamento fixtures...$(NC)"
	$(SYMFONY) doctrine:fixtures:load --no-interaction
	@echo "$(GREEN)✓ Fixtures caricate$(NC)"

# =============================================================================
# QUALITY (Test, Lint, Static Analysis)
# =============================================================================

test: ## Esegue tutti i test
	@echo "$(GREEN)Esecuzione test...$(NC)"
	cd backend && vendor/bin/phpunit
	@echo "$(GREEN)✓ Test completati$(NC)"

test-unit: ## Esegue solo i test unitari
	cd backend && vendor/bin/phpunit --testsuite=Unit

test-integration: ## Esegue solo i test di integrazione
	cd backend && vendor/bin/phpunit --testsuite=Integration

test-coverage: ## Esegue i test con coverage report
	@echo "$(GREEN)Esecuzione test con coverage...$(NC)"
	cd backend && vendor/bin/phpunit --coverage-html var/coverage
	@echo "$(GREEN)✓ Report generato in backend/var/coverage$(NC)"

lint: ## Verifica lo stile del codice (senza modificare)
	@echo "$(GREEN)Verifica code style...$(NC)"
	cd backend && vendor/bin/php-cs-fixer fix --dry-run --diff
	@echo "$(GREEN)✓ Verifica completata$(NC)"

fix: ## Corregge automaticamente lo stile del codice
	@echo "$(GREEN)Correzione code style...$(NC)"
	cd backend && vendor/bin/php-cs-fixer fix
	@echo "$(GREEN)✓ Code style corretto$(NC)"

stan: ## Esegue l'analisi statica (PHPStan)
	@echo "$(GREEN)Analisi statica...$(NC)"
	cd backend && vendor/bin/phpstan analyse
	@echo "$(GREEN)✓ Analisi completata$(NC)"

qa: lint stan test ## Esegue tutti i controlli di qualità (lint + stan + test)
	@echo "$(GREEN)✓ Tutti i controlli superati$(NC)"

# =============================================================================
# SETUP
# =============================================================================

jwt-keys: ## Genera le chiavi JWT per l'autenticazione
	@echo "$(GREEN)Generazione chiavi JWT...$(NC)"
	$(SYMFONY) lexik:jwt:generate-keypair --overwrite
	@echo "$(GREEN)✓ Chiavi JWT generate$(NC)"

# =============================================================================
# INIT (Prima installazione)
# =============================================================================

init: ## Setup iniziale completo del progetto
	@echo "$(GREEN)========================================$(NC)"
	@echo "$(GREEN)  BalanceBite - Setup Iniziale$(NC)"
	@echo "$(GREEN)========================================$(NC)"
	@echo ""
	@make start
	@make install
	@make jwt-keys
	@make db-create
	@make migrate
	@echo ""
	@echo "$(GREEN)✓ Setup completato!$(NC)"
	@echo ""
	@echo "Avvia il server con: $(BLUE)make serve$(NC)"
	@echo ""

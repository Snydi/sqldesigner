.PHONY: install up down reinstall _wait_postgres _composer_install _npm_install _run_migrations

install:
	@echo "Starting installation..."
	$(MAKE) down
	$(MAKE) up
	$(MAKE) _wait_postgres
	$(MAKE) _composer_install
	$(MAKE) _npm_install
	$(MAKE) _run_migrations
	@echo "Installation complete."

up:
	@echo "Starting containers..."
	docker-compose -p snydiagram up -d --build

down:
	@echo "Stopping containers..."
	docker-compose -p snydiagram down

_wait_postgres:
	@echo "Waiting for PostgreSQL to be ready..."
	@docker-compose -p snydiagram exec -T postgres sh -c 'until pg_isready -U $${POSTGRES_USER:-postgres} -d $${POSTGRES_DB:-postgres}; do sleep 2; echo "Waiting for PostgreSQL..."; done'
	@echo "PostgreSQL is ready!"
	@docker-compose -p snydiagram exec -T postgres sh -c 'psql -U $${POSTGRES_USER:-postgres} -tc "SELECT 1 FROM pg_database WHERE datname = '\''$${POSTGRES_DB:-postgres}'\''" | grep -q 1 || psql -U $${POSTGRES_USER:-postgres} -c "CREATE DATABASE $${POSTGRES_DB:-postgres}"'

_composer_install:
	@echo "Installing PHP dependencies..."
	docker-compose -p snydiagram exec -T php sh -c "composer install && php artisan key:generate"

_npm_install:
	@echo "Installing Node dependencies..."
	docker-compose -p snydiagram exec -T node npm install

_run_migrations:
	@echo "Running migrations..."
	docker-compose -p snydiagram exec -T php sh -c "php artisan migrate:fresh"

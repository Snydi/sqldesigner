.PHONY: install up down reinstall clean _wait_postgres _composer_install phpunit

install:
	docker-compose -p snydiagram build --no-cache --pull
	docker-compose -p snydiagram up -d --force-recreate
	$(MAKE) _wait_postgres
	$(MAKE) _composer_install
	docker-compose -p snydiagram exec -T php sh -c "cd /var/www/html/backend && php artisan key:generate"
	docker-compose -p snydiagram exec -T php sh -c "cd /var/www/html/backend && php artisan migrate:fresh --force"

up:
	docker-compose -p snydiagram up

down:
	docker-compose -p snydiagram down

clean:
	-docker-compose -p snydiagram down --rmi all --volumes --remove-orphans
	docker volume rm -f snydiagram_pgdata 2> nul
	docker system prune -a --volumes --force
	@if exist backend\storage\logs\laravel.log del /f /q backend\storage\logs\laravel.log
	@if exist backend\vendor rmdir /s /q backend\vendor 2> nul
	@if exist frontend\node_modules rmdir /s /q frontend\node_modules 2> nul

reinstall:
	$(MAKE) clean
	$(MAKE) install

test:
	docker-compose -p snydiagram exec -T php sh -c "cd /var/www/html/backend && vendor/bin/phpunit"

test_coverage:
	docker-compose exec php sh -c "cd /var/www/html/backend && vendor/bin/phpunit --coverage-html=tests/coverage"

_wait_postgres:
	docker-compose -p snydiagram exec -T postgres sh -c 'until pg_isready -U $${POSTGRES_USER:-postgres} -d $${POSTGRES_DB:-postgres}; do sleep 2; echo "Waiting for PostgreSQL..."; done'
	docker-compose -p snydiagram exec -T postgres sh -c 'psql -U $${POSTGRES_USER:-postgres} -tc "SELECT 1 FROM pg_database WHERE datname = '\''$${POSTGRES_DB:-snydiagram}'\''" | grep -q 1 || psql -U $${POSTGRES_USER:-postgres} -c "CREATE DATABASE $${POSTGRES_DB:-snydiagram}"'

_composer_install:
	docker-compose -p snydiagram exec -T php sh -c "\
		cd /var/www/html/backend && \
		composer clear-cache && \
		composer install --no-interaction --prefer-dist --no-suggest --no-progress --optimize-autoloader"
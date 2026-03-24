.PHONY: install up down reinstall clean _wait_postgres _composer_install phpunit \
        install-prod up-prod down-prod build-frontend _wait_postgres_prod _composer_install_prod \
        clean-prod reinstall-prod

ifeq ($(OS),Windows_NT)
    RM = del /f /q
    RMDIR = rmdir /s /q
    SEP = \\
    DEVNULL = nul
    TRUE = (exit 0)
else
    RM = rm -f
    RMDIR = rm -rf
    SEP = /
    DEVNULL = /dev/null
    TRUE = true
endif

install:
	docker-compose -p snydiagram build --no-cache --pull
	docker-compose -p snydiagram up -d --force-recreate
	$(MAKE) _wait_postgres
	$(MAKE) _wait_mysql
	$(MAKE) _composer_install
	docker-compose -p snydiagram exec -T php sh -c "cd /var/www/html/backend && php artisan key:generate"
	docker-compose -p snydiagram exec -T php sh -c "cd /var/www/html/backend && php artisan migrate:fresh --force"

up:
	docker-compose -p snydiagram up

down:
	docker-compose -p snydiagram down

clean:
	-docker-compose -p snydiagram down --rmi all --volumes --remove-orphans
	docker volume rm -f snydiagram_pgdata 2>$(DEVNULL) || $(TRUE)
	docker system prune -a --volumes --force
	-$(RM) backend$(SEP)storage$(SEP)logs$(SEP)laravel.log
	-$(RMDIR) backend$(SEP)vendor
	-$(RMDIR) frontend$(SEP)node_modules

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

_wait_mysql:
	docker-compose -p snydiagram exec -T mysql_test sh -c 'until mysqladmin ping -h localhost -u root -proot --silent; do sleep 2; echo "Waiting for MySQL..."; done'

_composer_install:
	docker-compose -p snydiagram exec -T php sh -c "\
		cd /var/www/html/backend && \
		composer clear-cache && \
		composer install --no-interaction --prefer-dist --no-suggest --no-progress --optimize-autoloader"

# ── Production ────────────────────────────────────────────────────────────────

build-frontend:
	docker run --rm \
		-v "$(CURDIR)/frontend":/app \
		-w /app \
		node:18-alpine \
		sh -c "npm ci && npm run build"
	mkdir -p backend/public/build backend/public/images
	cp -r frontend/public/build/. backend/public/build/
	cp frontend/src/icons/logo.svg backend/public/images/logo.svg
	cp frontend/src/icons/screenshot.png backend/public/images/screenshot.png
	docker run --rm \
		-v "$(CURDIR)/frontend/src/icons":/src \
		-v "$(CURDIR)/backend/public/images":/out \
		-w /src \
		node:18-alpine \
		sh -c "npm install -g sharp-cli 2>/dev/null; sharp -i screenshot.png -o /out/screenshot.webp"
	docker run --rm \
		-v "$(CURDIR)/backend/public":/out \
		node:18-alpine \
		sh -c "npm install -g sharp-cli 2>/dev/null; \
			sharp -i /out/favicon.svg -o /out/favicon-48x48.png resize 48 48; \
			sharp -i /out/favicon.svg -o /out/favicon-96x96.png resize 96 96; \
			sharp -i /out/favicon.svg -o /out/favicon-192x192.png resize 192 192; \
			sharp -i /out/favicon.svg -o /out/apple-touch-icon.png resize 180 180"
	-$(RM) backend$(SEP)public$(SEP)hot 2>$(DEVNULL)
	-$(RM) frontend$(SEP)public$(SEP)hot 2>$(DEVNULL)

install-prod:
	$(MAKE) build-frontend
	docker compose -f docker-compose.prod.yml -p snydiagram build --no-cache
	docker compose -f docker-compose.prod.yml -p snydiagram up -d --force-recreate
	$(MAKE) _wait_postgres_prod
	$(MAKE) _composer_install_prod
	docker exec --user www php sh -c "\
		cd /var/www/html/backend && \
		php artisan key:generate --no-interaction && \
		php artisan migrate --force && \
		php artisan optimize"

up-prod:
	docker compose -f docker-compose.prod.yml -p snydiagram up -d

down-prod:
	docker compose -f docker-compose.prod.yml -p snydiagram down

deploy:
	git fetch origin
	git reset --hard origin/master
	$(MAKE) build-frontend
	docker exec php sh -c "\
		cd /var/www/html/backend && \
		composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader"
	docker exec --user www php sh -c "\
		cd /var/www/html/backend && \
		php artisan migrate --force && \
		php artisan optimize"
	docker compose -f docker-compose.prod.yml -p snydiagram restart php queue
	docker exec nginx sh -c "mkdir -p /tmp/nginx_fastcgi_cache && nginx -s reload"
	sleep 2
	curl -s -o /dev/null https://sql-designer.com/ || true
	curl -s -o /dev/null https://sql-designer.com/blog || true

_wait_postgres_prod:
	docker exec postgres sh -c \
		'until pg_isready -U $${POSTGRES_USER:-postgres} -d $${POSTGRES_DB:-postgres}; do sleep 2; echo "Waiting for PostgreSQL..."; done'

_composer_install_prod:
	docker exec php sh -c "\
		cd /var/www/html/backend && \
		composer install --no-interaction --prefer-dist --no-dev --no-suggest --no-progress --optimize-autoloader"

clean-prod:
	-docker compose -f docker-compose.prod.yml -p snydiagram down --rmi all --volumes --remove-orphans
	docker volume rm -f snydiagram_pgdata 2>$(DEVNULL) || $(TRUE)
	docker system prune -a --volumes --force

reinstall-prod:
	$(MAKE) clean-prod
	$(MAKE) install-prod
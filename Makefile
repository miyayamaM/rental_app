.PHONY: up down bash phpcbf

up:
	docker-compose up -d --force-recreate

down:
	docker-compose down

build:
	docker-compose build

bash:
	docker-compose exec app bash

phpcbf:
	docker-compose exec app ./vendor/bin/phpcbf --standard=phpcs.xml ./

stan:
	docker-compose exec app ./vendor/bin/phpstan analyse --memory-limit=2G

migrate:
	docker-compose exec app php artisan migrate

test:
	docker-compose exec app php artisan migrate --env=testing
	docker-compose exec app php artisan test --env=testing

dusk:
	docker-compose exec app php artisan migrate --env=testing
	docker-compose exec app php artisan dusk:chrome-driver
	docker-compose exec app chmod -R 0755 vendor/laravel/dusk/bin
	docker-compose exec app php artisan dusk --env=testing

run: install
	docker-compose exec app php artisan optimize:clear
	docker-compose exec app php artisan migrate
	docker-compose ps -a

build: install
	docker-compose exec app php artisan optimize:clear
	docker-compose exec app php artisan optimize
	docker-compose exec app php artisan migrate
	docker-compose ps -a

install: down
	ls .data || mkdir .data
	ls .env || cp .env.example .env
	USER=$(USER) docker-compose up -d --build
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate

status:
	docker-compose ps -a

down:
	docker-compose down
	docker-compose ps -a

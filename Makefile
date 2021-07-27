include .env
#Поднять проект локально
up: docker-compose.yml
	docker-compose up -d

#Пересоздать образы, почистить кэш
recreate: docker-compose.yml
	docker-compose rm -f
	docker-compose pull
	docker-compose up --build -d
#Показать все контейнеры
ps: docker-compose.yml
	docker-compose ps

#Остановить проект
down: docker-compose.yml
	docker-compose down

init: docker-compose.yml
	docker-compose exec php composer install
	docker-compose exec -T mysql sh -c 'exec mysql -u${MYSQL_USER} -p"${MYSQL_PASSWORD}"' < data/mysql/dump/init_database.sql
	docker-compose exec php bin/console doctrine:schema:create
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction



up:
	docker-compose --compatibility up --build -d  1> /dev/null && docker-compose ps

down: 
	docker-compose down --remove-orphans
	
test:
	docker run php:7.4-fpm-buster -v ${PWD}:/var/app -w /var/app --rm php -v

install:
	docker run composer:2 --rm -ti --volume ${PWD}:/app -w /app install

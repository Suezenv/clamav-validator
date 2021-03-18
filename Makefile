

up:
	docker-compose --compatibility up --build -d  1> /dev/null && docker-compose ps

down: 
	docker-compose down --remove-orphans
	
test:
	docker-compose run --rm --w /app php vendor/bin/phpunit

install:
	docker run  --rm --volume ${PWD}:/app -w /app composer:2 install

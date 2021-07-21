IMAGE_CLAMAV=3snetregistry.azurecr.io/tsmx/clamav:latest


up: image-pull
	docker-compose --compatibility up --build -d  1> /dev/null && docker-compose ps

down: 
	docker-compose down --remove-orphans
	
test:
	docker-compose run --rm --w /app php vendor/bin/phpunit

install:
	docker run  --rm --volume ${PWD}:/app -w /app composer:2 install

image-build:
	docker build -t $(IMAGE_CLAMAV) --file docker/clamav/Dockerfile docker/clamav

image-push:
	docker push $(IMAGE_CLAMAV)

image-pull:
	docker pull $(IMAGE_CLAMAV)
build:
	$(info Make: Building image.)
	cp composer.* .docker/php-fpm/
	docker-compose build --no-cache --force-rm --pull

start:
	$(info Make: Starting application.)
	docker-compose up

stop:
	$(info Make: Stopping application.)
	docker-compose down

daemonize:
	$(info Make: Starting application in background.)
	docker-compose up -d

logs:
	docker-compose logs -f


clean:
	@docker system prune --volumes --force



COMPOSE_PROJECT_NAME?=ab-testing
COMPOSE_FILE?=docker/docker-compose.yml


build:
#	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} build --no-cache --force-rm
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} build

run:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up --detach
#	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up

stop:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} down

cli:
	docker exec -it  ab-test-php-fpm bash

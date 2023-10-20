#!/usr/bin/env bash

PROJECT_NAME = gas

DOCKER_COMPOSE = docker-compose -p $(PROJECT_NAME)

CONTAINER_NGINX = $$(docker container ls -f "name=$(PROJECT_NAME)_nginx" -q)
CONTAINER_PHP = $$(docker container ls -f "name=$(PROJECT_NAME)_php" -q)
CONTAINER_NODE = $$(docker container ls -f "name=$(PROJECT_NAME)_node" -q)
CONTAINER_DB = $$(docker container ls -f "name=$(PROJECT_NAME)_database" -q)

NGINX = docker exec -ti $(CONTAINER_NGINX)
PHP = docker exec -ti $(CONTAINER_PHP)
NODE = docker exec -ti $(CONTAINER_NODE)
DATABASE = docker exec -ti $(CONTAINER_DB)

COLOR_RESET			= \033[0m
COLOR_ERROR			= \033[31m
COLOR_INFO			= \033[32m
COLOR_COMMENT		= \033[33m
COLOR_TITLE_BLOCK	= \033[0;44m\033[37m

help:
	@printf "${COLOR_TITLE_BLOCK}Makefile${COLOR_RESET}\n"
	@printf "\n"
	@printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	@printf " make [target]\n\n"
	@printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	@awk '/^[a-zA-Z\-\_0-9\@]+:/ { \
		helpLine = match(lastLine, /^## (.*)/); \
		helpCommand = substr($$1, 0, index($$1, ":")); \
		helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
		printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Kill all containers
kill:
	@$(DOCKER_COMPOSE) kill $(CONTAINER) || true

## Build containers
build:
	@$(DOCKER_COMPOSE) build --pull --no-cache

## Init project
init: install update

## Start containers
start:
	@$(DOCKER_COMPOSE) up -d
	@echo "admin is available here: 'https://back.traefik.me/admin'"
	@echo "front is available here: 'https://front.traefik.me'"

## Stop containers
stop:
	@$(DOCKER_COMPOSE) down

restart: stop start

## Init project
init: install update drop create migrate migration migrate fixture npm-install npm-build jwt-overwrite

## Init project
init-db: drop create migrate migration migrate fixture

jwt:
	$(PHP) bin/console lexik:jwt:generate-keypair --skip-if-exists

jwt-overwrite:
	$(PHP) bin/console lexik:jwt:generate-keypair --overwrite

cache:
	$(PHP) rm -r var/cache

## Entering php shell
php:
	@$(DOCKER_COMPOSE) exec php sh

node:
	@$(DOCKER_COMPOSE) exec node sh

## Entering nginx shell
nginx:
	@$(DOCKER_COMPOSE) exec nginx sh

## Entering database shell
database:
	@$(DOCKER_COMPOSE) exec database sh

## Composer install
install:
	$(PHP) composer install

## Composer update
update:
	$(PHP) composer update

npm-install:
	$(PHP) npm install

npm-build:
	$(PHP) npm run build

## Drop database
drop:
	$(PHP) bin/console doctrine:database:drop --if-exists --force

## Load fixtures
fixture:
	$(PHP) bin/console hautelook:fixtures:load --env=dev --no-interaction

## Create database
create:
	$(PHP) bin/console doctrine:database:create --if-not-exists

## Making migration file
migration:
	$(PHP) bin/console make:migration

## Applying migration
migrate:
	$(PHP) bin/console doctrine:migration:migrate --no-interaction

price-download:
	$(PHP) bin/console app:gas-price:download

price-update:
	$(PHP) bin/console app:gas-price:update

status-update:
	$(PHP) bin/console app:gas-status:update

status-anomaly:
	$(PHP) bin/console app:gas-status:anomaly

## QA
cs-fixer:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa php-cs-fixer fix ./src --rules=@Symfony

cs-fixer-dry:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa php-cs-fixer fix ./src --rules=@Symfony --dry-run

phpcpd:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa phpcpd ./src

phpstan:
	docker run --init -it --rm -v $(PWD):/project -w /project jakzal/phpqa phpstan analyse ./src --level=5

## Starting consumer
consume:
	$(PHP) bin/console messenger:consume async_priority_high async_priority_medium async_priority_low -vv

consume-high:
	$(PHP) bin/console messenger:consume async_priority_high -vv

consume-medium:
	$(PHP) bin/console messenger:consume async_priority_medium -vv

consume-low:
	$(PHP) bin/console messenger:consume async_priority_low -vv

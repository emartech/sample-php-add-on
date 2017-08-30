DOCKER = docker
DOCKER_COMPOSE = docker-compose
CONTAINER = sample-iframe-integration
DB_CONTAINER = sample-iframe-database

help: ## Show this help
	@echo "Targets:"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/\(.*\):.*##[ \t]*/    \1 ## /' | sort | column -t -s '##'

all:
	make stop
	make build
	make start
	sleep 10
	make patch-db

build: ## Build containers
	$(DOCKER_COMPOSE) -f docker/docker-compose.yaml build --no-cache

stop: ## Stop containers
	$(DOCKER_COMPOSE) -f docker/docker-compose.yaml down

start: ## Start containers
	$(DOCKER_COMPOSE) -f docker/docker-compose.yaml up -d

restart: stop start ## Restart containers

bash: ## Open shell in projec's container as root
	$(DOCKER_COMPOSE) -f docker/docker-compose.yaml exec $(CONTAINER) /bin/bash

psql: ## Start PostgreSQL client
	$(DOCKER_COMPOSE) -f docker/docker-compose.yaml exec $(DB_CONTAINER) /bin/bash -l -c "psql -U my_user -W -d my_database"

patch-db: ## Patch database
	$(DOCKER_COMPOSE) -f docker/docker-compose.yaml  exec $(CONTAINER) /bin/bash -l -c "php init_schema.php 2>&1"

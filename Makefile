ENV := $(PWD)/.env

# Environment variables for project
include $(ENV)

# Export all variable to sub-make
export

DOCKER_COMPOSE  = docker-compose

EXEC_PHP        = $(DOCKER_COMPOSE) exec -T php
EXEC_JS        = $(DOCKER_COMPOSE) exec -T php

SYMFONY         = $(EXEC_PHP) bin/console
COMPOSER        = $(EXEC_PHP) composer
YARN            = $(EXEC_JS) yarn
NPM				= $(EXEC_JS) npm

##
## Project
## -------
##

build:
	@$(DOCKER_COMPOSE) pull --ignore-pull-failures
	$(DOCKER_COMPOSE) build --pull

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: ## Install and start the project
install: .env.local network build start vendor assets success

network: ## Create network for project
	docker network create $(PROJECT_NAME)_network || true

reset: ## Stop and start a fresh install of the project
reset: kill install

start: ## Start the project
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate

stop: ## Stop the project
	$(DOCKER_COMPOSE) stop

clean: ## Stop the project and remove generated files
clean: kill
	rm -rf .env.local vendor node_modules

success:
	@echo '\033[1;32mInstall done\033[0m';

.PHONY: build kill install reset start stop clean success

##
## Utils
## -----
##
cache:
cache:
	-$(SYMFONY) cache:clear --no-warmup

db: ## Reset the database and load fixtures
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:schema:update --force
	$(SYMFONY) doctrine:fixtures:load -n
	@echo '\033[1;32mDatabase up\033[0m';

db_test: ## Reset the test database and load fixtures
	$(SYMFONY) --env=test doctrine:database:drop --if-exists --force
	$(SYMFONY) --env=test doctrine:database:create --if-not-exists
	$(SYMFONY) --env=test doctrine:schema:update --force
	$(SYMFONY) --env=test doctrine:fixtures:load -n
	@echo '\033[1;32mDatabase up\033[0m';

fixture: ## Reload fixtures
fixture:
	$(SYMFONY) doctrine:fixtures:load --no-interaction
	@echo '\033[1;32mFixtures loaded\033[0m';

migration: ## Generate a new doctrine migration
	$(SYMFONY) doctrine:migrations:diff
	$(SYMFONY) doctrine:schema:validate

assets: vendor
	$(SYMFONY) assets:install public

update-composer: ## update-composer
update-composer:
	$(COMPOSER) update

dump-js-routes:
	$(SYMFONY) fos:js-routing:dump --format=json --target=assets/js/fos_js_routes.json

lint-js:
	$(YARN) eslint assets

fix-js:
	$(YARN) eslint assets --fix

##
## Tests
## -----
##

test: ## Run tests
test: phpunit

phpunit: vendor
	$(EXEC_PHP) bin/phpunit --testdox

test-coverage: ## Run PHPUnit tests (coverage)
	$(EXEC_PHP) php -d memory_limit=-1 bin/phpunit --coverage-text --coverage-html=build/

cs: ## Run PHP-CS-Fixer (dry-run)
	$(EXEC_PHP) vendor/bin/ecs check

cs-fix: ## Run PHP-CS-Fixer
	$(EXEC_PHP) vendor/bin/ecs check --fix

sa: ## Run PHPStan
	$(EXEC_PHP) vendor/bin/phpstan --ansi -v

sa-baseline: ## Re-generate PHPStan baseline (!risky!)
	$(EXEC_PHP) vendor/bin/phpstan --generate-baseline

rector: ## Run Rector
	$(EXEC_PHP) vendor/bin/rector process src

# rules based on files
composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(COMPOSER) install

package-lock.json: package.json
	$(NPM) upgrade

node_modules: package-lock.json
	$(NPM) install
	$(NPM) run dev
	@touch -c node_modules

npm_watch:
	$(NPM) run watch

npm_dev:
	$(NPM) run dev

yarn_install:
	$(YARN) install
	$(YARN) run dev

yarn_watch:
	$(YARN) run watch

yarn_dev:
	$(YARN) run dev

compile-assets: ## Compile assets
	$(SYMFONY) asset-map:compile

.env.local: .env
	@if [ -f .env.local ]; \
	then\
		echo '\033[1;41m/!\ The .env file has changed. Please check your .env.local file.\033[0m';\
		diff .env .env.local;\
		touch .env.local;\
		exit 1;\
	else\
		echo cp .env .env.local;\
		cp .env .env.local;\
	fi

env_dev: .env.local dump_env_dev

dump_env_dev: ## Generate .env.local.php for dev
	$(COMPOSER) dump-env dev

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

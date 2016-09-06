.SILENT:
.PHONY: help

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

#########
# Setup #
#########

## Setup environment & Install application
setup: provision
	vagrant ssh -c 'cd /srv/app/symfony && composer self-update && make install'

setup@test: provision@test install@test

#############
# Provision #
#############

## Provision environment
provision: provision-vagrant

provision@test: provision-ansible@test

provision-services@test: provision-services-ansible@test

provision-vagrant:
	ansible-galaxy install -r ansible/roles.yml -p ansible/roles -f
	vagrant up --no-provision
	vagrant provision

provision-ansible@test:
	ansible-galaxy install -r ansible/roles.yml -p ansible/roles -f
	ansible-playbook -i ansible/hosts -l env_test,app -s -e "_user=${_ANSIBLE_USER}" --force-handlers ansible/playbook.yml

provision-services-ansible@test:
	ansible-playbook -i ansible/hosts -l env_test,app -s -e "_user=${_ANSIBLE_USER}" --force-handlers --tags=elao_services ansible/playbook.yml

###########
# Install #
###########

## Install application
install: install-app install-db install-db@test install-db-fixtures install-db-fixtures@test 

install@test: install-app@test install-db@test install-db-fixtures@test install-dep build@prod

install@prod: install-dep build@prod

install-app:
	composer --no-progress --no-interaction install

install-app@test:
	SYMFONY_ENV=test composer --no-progress --no-interaction install

install-db:
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:update --force

install-db@test:
	bin/console doctrine:database:drop --force --if-exists --env=test
	bin/console doctrine:database:create --env=test
	bin/console doctrine:schema:create --env=test

install-db-fixtures:
	#bin/console doctrine:fixtures:load -n

install-db-fixtures@test:
	#bin/console doctrine:fixtures:load -n --env=test

install-dep:
	npm --no-spin install

#########
# Build #
#########

## Build application
build: build-assets

build@prod: build-assets@prod

build-assets:
	gulp --dev

build-assets@prod:
	gulp

########
# Test #
########

## Run tests
test: test-phpunit test-behat

test@test: test-phpunit@test test-behat@test

test-phpunit:
	bin/phpunit

test-phpunit@test:
	rm -rf var/tests/junit.xml var/tests/clover.xml var/tests/coverage
	stty cols 80; bin/phpunit --log-junit var/tests/junit.xml --coverage-clover var/tests/clover.xml --coverage-html var/tests/coverage

test-behat:
	bin/behat

test-behat@test:
	bin/behat --format=progress --no-interaction
	
########
# Lint #
########

## Linting
lint:
    phpcs src --standard=PSR2

##########
# Deploy #
##########

## Deploy application (demo)
deploy@demo: deploy-capifony@demo

## Deploy application (prod)
deploy@prod: deploy-capifony@prod

deploy-capifony@demo:
	cap demo deploy

deploy-capifony@prod:
	cap prod deploy

##########
# Custom #
##########

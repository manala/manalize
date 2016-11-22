.SILENT:
.PHONY: build test

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

###############
# Environment #
###############

## Setup environment & Install & Build application
setup:
	if [ -d  "./var/cache" ]; then rm -rf ./var/cache; fi;
	if [ -d "./var/logs" ]; then rm -rf ./var/logs; fi;
	if [ -d "./var/sessions" ]; then rm -rf ./var/sessions; fi;
	vagrant up --no-provision
	vagrant provision
	vagrant ssh -- "cd /srv/app && make install && make build"

## Update environment
update: export ANSIBLE_TAGS = manala.update
update:
	vagrant provision

## Update ansible
update-ansible: export ANSIBLE_TAGS = manala.update
update-ansible:
	vagrant provision --provision-with ansible

## Provision environment
provision: export ANSIBLE_EXTRA_VARS = {"manala":{"update":false}}
provision:
	vagrant provision --provision-with app

## Provision nginx
provision-nginx: export ANSIBLE_TAGS = manala_nginx
provision-nginx: provision

## Provision php
provision-php: export ANSIBLE_TAGS = manala_php
provision-php: provision

###########
# Install #
###########

## Install application
install:
	composer install --verbose
	bin/console doctrine:database:create --if-not-exists

install@test: export SYMFONY_ENV = test
install@test:
	# Composer
	composer install --verbose --no-progress --no-interaction
	# Doctrine
	bin/console doctrine:database:drop --force --if-exists
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:update --force

install@demo: export SYMFONY_ENV = prod
install@demo:
	# Composer
	composer install --verbose --no-progress --no-interaction --prefer-dist --optimize-autoloader
	# Symfony cache
	bin/console cache:warmup --no-debug
	# Doctrine migrations
	bin/console doctrine:migrations:migrate --no-debug --no-interaction

install@prod: export SYMFONY_ENV = prod
install@prod:
	# Composer
	composer install --verbose --no-progress --no-interaction --prefer-dist --optimize-autoloader --no-dev
	# Symfony cache
	bin/console cache:warmup --no-debug
	# Doctrine migrations
	bin/console doctrine:migrations:migrate --no-debug --no-interaction

##########
# Build #
##########

build:
	if [ -f "gulpfile" ]; then gulp --dev; fi;
build@demo:
	if [ -f "gulpfile" ]; then gulp; fi;
build@prod:
	if [ -f "gulpfile" ]; then gulp; fi;

##########
# Deploy #
##########

## Deploy application
deploy@demo:
	ansible-playbook ansible/deploy.yml --inventory-file=ansible/hosts --limit=deploy_demo
deploy@prod:
	ansible-playbook ansible/deploy.yml --inventory-file=ansible/hosts --limit=deploy_prod

##########
# Custom #
##########

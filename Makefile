CONSOLE = php bin/console

install: yarn_install init fixtures

install_full: bdd_init init yarn_install fixtures

init:
	composer install --optimize-autoloader --no-interaction
	$(CONSOLE) d:d:c --if-not-exists
	$(CONSOLE) d:s:u --force
	$(CONSOLE) ckeditor:install --no-interaction
	$(CONSOLE) assets:install --symlink

fixtures:
	$(CONSOLE) doctrine:fixtures:load --no-interaction -v

yarn_install:
	yarn install
	yarn iconfont
	yarn build
	yarn admin-build
	yarn super-admin-build

# Compile all front files
yarn_dev_all:
	yarn iconfont
	yarn dev
	yarn admin-dev
	yarn super-admin-dev

composer_install:
	composer install --optimize-autoloader --no-interaction

clean:
	rm -rf .git
	rm README.md
	git init

dsu:
	$(CONSOLE) d:s:u --force

bdd_init:
	$(CONSOLE) d:d:c --if-not-exists
	$(CONSOLE) d:s:d --force
	$(CONSOLE) d:s:u --force

bdd_cc:
	$(CONSOLE) doctrine:cache:clear-query --env=dev
	$(CONSOLE) doctrine:cache:clear-result --env=dev
	$(CONSOLE) doctrine:cache:clear-metadata --env=dev

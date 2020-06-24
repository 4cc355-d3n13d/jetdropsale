ROOT_DIR := $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
OS := $(shell uname)

default: start_dev

start:
	@(echo "-> Starting application docker (local)")
	docker-compose -f docker-compose.yml up -d
	@(echo "-> Done")

start_dev:
	@(echo "-> Starting application docker (dev mode)")
	# make images_update
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml up -d
	make migrate
	# make composer_install

start_sync:
	@(echo "-> Starting application docker (synced)")
	make images_update
	docker volume create --name=web-app-native-osx-sync
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml -f docker-compose-sync.yml up -d
	make migrate
	docker-sync start
	make composer_install

stop:
	@(echo "-> Stopping application docker (sync on mac)...")
ifeq ($(OS),Darwin)
	docker-compose down
	docker-sync clean
else
	docker-compose stop
endif
	@(echo "-> Done")

images_update:
	@(echo "-> Updating docker images...")

	docker pull registry.cpadev.com:4567/dropwow2/docker/nginx
	docker pull registry.cpadev.com:4567/dropwow2/docker/php-fpm
	docker pull registry.cpadev.com:4567/dropwow2/docker/elastic/oss
	docker pull redis:alpine
	@(echo "-> Done")

migrate:
	@(echo "-> Running migrations...")
	@(./do.sh -c "php artisan migrate")
	@(echo "-> Done")

composer_install:
	@(echo "-> Installing composer dependencies...")
	@(./do.sh -c "composer install")
	@(echo "-> Done")

composer_update:
	@(echo "-> Updating composer dependencies...")
	@(./do.sh -c "composer update")
	@(echo "-> Done")

lint:
	@(echo "-> Running php lint...")
	@(./vendor/bin/phpcs --standard=ruleset.xml app -p)
	@(echo "-> Done")

test:
	@(echo "-> Running tests (dockered)...")
	@(./do.sh -c ./phpunit)
	@(echo "-> Done")

grum:
	@(echo "-> Running GrumPHP (dockered)..")
	@(./do.sh -c "./vendor/bin/grumphp run")
	@(echo "-> Done")

clean:
	@(echo "-> Cleaning caches (dockered)...")
	@(./do.sh -c "php artisan cache:clear")
	@(./do.sh -c "php artisan config:clear")
	@(./do.sh -c "php artisan view:clear")
	@(./do.sh -c "php artisan route:clear")
	@(./do.sh -c "php artisan debugbar:clear")
	@(echo "-> Done")

swagger:
	@(echo "-> Updating swagger docs (dockered)...")
	@(./do.sh -c "php artisan l5-swagger:generate")
	@(echo "-> Done")

job_reindex:
	@(echo "-> Add reindex data job and start queue worker (dockered)...")
	./do.sh -c "php artisan scout:import \"App\Models\User\""
	./do.sh -c "php artisan queue:work"

ide:
	@(echo "-> IDE helper: make `_ide_helper.php`, write models properties...")
	php artisan ide-helper:models -W -R
	@(echo "-> Done")

ide_full:
	@(echo "-> IDE helper: generating all smelly things...")
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models -W -R
	@(echo "-> Done")

tools_update:
	composer self-update
	deployer self-update

start_fire:
	@(echo "-> Starting application docker with BlackFire")
	docker-compose -f docker-compose.yml -f docker-compose-fire.yml up -d
	@(echo "-> Done")

stop_fire:
	@(echo "-> Stopping application docker with BlackFire")
	docker-compose -f docker-compose.yml -f docker-compose-fire.yml stop
	@(echo "-> Done")

.PHONY: ide code_fix tools_update code_check job_reindex test swagger grum lint

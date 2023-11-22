config:
	@sed -e 's/<userId>/'`id -u`'/' -e 's/<groupId>/'`id -g`'/' -e 's/<username>/'`whoami`'/' .env.dist > .env

build: config
	@docker compose build
	@docker compose run php composer install

php:
	@docker compose run -it php /bin/bash

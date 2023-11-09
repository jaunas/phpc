config:
	@sed -e 's/<userId>/'`id -u`'/' -e 's/<groupId>/'`id -g`'/' .env.dist > .env

build: config
	@docker compose build

php:
	@docker compose run -it php /bin/bash

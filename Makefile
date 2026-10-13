.PHONY: setup up down restart logs bash artisan composer npm test migrate seed clean

setup:
	@chmod +x docker-dev.sh
	@./docker-dev.sh setup

up:
	@./docker-dev.sh up

down:
	@./docker-dev.sh down

restart:
	@./docker-dev.sh restart

status:
	@./docker-dev.sh status

logs:
	@./docker-dev.sh logs

bash:
	@./docker-dev.sh bash

test:
	@./docker-dev.sh test

migrate:
	@./docker-dev.sh migrate

seed:
	@./docker-dev.sh seed

clean:
	@./docker-dev.sh clean

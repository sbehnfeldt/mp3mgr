include .env
.PHONY: build-full up create start restart stop

# Build images from Dockerfiles
build :
	docker compose -p ${PROJECT_NAME} build

# Create containers from images
create :
	docker compose -p ${PROJECT_NAME} create

up :
	docker compose -p ${PROJECT_NAME} up -d

down :
	docker compose -p ${PROJECT_NAME} down

start :
	docker compose -p ${PROJECT_NAME} start

restart :
	docker compose -p ${PROJECT_NAME} restart

stop:
	docker compose -p ${PROJECT_NAME} stop

# Create an image from the Dockerfiles specified for each service in the docker-compose.yml file
build-full:
	echo ${PROJECT_NAME}
	docker compose -p ${PROJECT_NAME} build --no-cache

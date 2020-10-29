# carsis-project
- `13.05.2019 - 27.05.2019` - This is an internal project by UptetiX/Scalefocus. The assignment was to create a RESTfull Api for car ads and used for practice by the upcoming frontenders. Prerequisites: JWT technology, SWAGGER, predefined data for `brand` and `model` tables.
- `26.10.2020 - 29.10.2020` - The project was return to me without git and the devops template after I quit the company. I made a new repository and used docker compose to run the project.
- Note: To authorise all routes in the api doc UI `http://carsis-project/api/doc` go to `Auth` section and use one of the two routes `api/auth/register/` or `api/auth/login` depending on what you need to get the JWT. When you copy it go to `Authorise` at top right and in the `Value` field before you paste the token write `Bearer ` with space. 

## Used Technologies
- PHP 7.3-fpm
- Symphony 4.2
- PDO
- JWT
- SWAGGER
- Nginx 1.18
- MySQL 5.7
- Bootstrap 3.3
- Composer 1.10
- Git 2.25
- Docker 19.03
- Docker-compose 1.27

## Setup Prerequisites
You must have the following tools installed:
- Git - https://git-scm.com/downloads
- Docker - https://docs.docker.com/install/linux/docker-ce/ubuntu/
- Docker Compose - https://docs.docker.com/compose/install/
- You must add the proper virtual host record to your /etc/hosts file: `127.0.0.1	carsis-project`
  
## Setup Configuration
- Configuration is in .env(will be created for you based on .env-dist) and there you can tweak database config and some Docker params.
- In case your uid and gid are not 1000 but say 1001, you must change the USER_ID and GROUP_ID vars in .env file. Type the `id` command in your terminal in order to find out.
- Nginx logs are accessible in ./volumes/nginx/logs
- MySQL data is persisted via a Docker volume.
- Composer cache is persisted via a Docker volume.
- You can write code by loading your project in your favourite IDE, but in order to use Composer you must work in the PHP container.

## Start the Docker ecosystem for a first time
- `mkdir carsis-project` - create a new project dir
- `cd carsis-project` - get into it
- `https://github.com/Slavchev070720/carsis-project.git .` - clone code from repo
- `cp .env-dist .env` - create the .env file
- Now you would want to run `id` command and set USER_ID and GROUP_ID env vars in .env file as per your needs.
- `docker-compose build` - build Docker images and volumes
- `docker-compose run --rm php-dev composer install` - install Composer packages
- `docker-compose up -d` - start the whole ecosystem (wait few seconds for mysql service to start)
- `docker-compose ps` - verify all containers are up and running
- `docker exec -it carsis-php-dev /bin/bash` - ssh into carsis-php-dev container to run sh script
- `devops/php-fpm/dev/scripts/commands.sh` - run sh script for database migration and JWT configuration
- Open your favorite browser and go to `http://carsis-project/api/doc` to see carsis-project api UI documentation.

### Useful commands
- `docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container` - gets container's IP
- `docker-compose exec carsis-php-dev /bin/bash` - enter the php container.
- `docker kill -s HUP container` - can be used to reload Nginx configuration dynamically
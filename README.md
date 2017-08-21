# Docker Symfony3 - API project

Used docker-symfony repository: https://github.com/maxpou/docker-symfony.git 

## Installation

1. Create a `.env` from the `.env.dist` file. Adapt it according to your symfony application

    ```bash
    cp .env.dist .env
    ```

2. Build/run containers with (with and without detached mode)

    ```bash
    $ docker-compose build
    $ docker-compose up -d
    # shut down containers:
    $ docker-compose down
    # see containers' status:
    $ docker-compose ps
    ```

3. Update your system host file (add electronic-store.dev)

    ```bash
    # UNIX only: get containers IP address and update host (replace IP according to your configuration) (on Windows, edit C:\Windows\System32\drivers\etc\hosts)
    $ sudo echo $(docker network inspect bridge | grep Gateway | grep -o -E '[0-9\.]+') "electronic-store.dev" >> /etc/hosts
    ```

4. Prepare Symfony app: see ElecStoreAPI/README.md

5. Enjoy :-)

## Usage

Just run `docker-compose up -d`, then:

* Symfony app: visit [electronic-store.dev](http://electronic-store.dev)  
* Symfony dev mode: visit [electronic-store.dev/app_dev.php](http://electronic-store.dev/app_dev.php)
* PhpMyAdmin: visit [electronic-store.dev:8080](http://electronic-store.dev:8080)
* Logs (files location): logs/nginx and logs/symfony
* Logs (Kibana - unused here): [electronic-store.dev:81](http://electronic-store.dev:81)

## How it works?

Have a look at the `docker-compose.yml` file, here are the `docker-compose` built images:

* `db`: This is the MySQL database container,
* `php`: This is the PHP-FPM container in which the application volume is mounted,
* `nginx`: This is the Nginx webserver container in which application volume is mounted too,
* `elk`: This is a ELK stack container which uses Logstash to collect logs, send them into Elasticsearch and visualize them with Kibana.

This results in the following running containers:

```bash
$ docker-compose ps
           Name                          Command               State              Ports            
--------------------------------------------------------------------------------------------------
dockersymfony_db_1            /entrypoint.sh mysqld            Up      0.0.0.0:3306->3306/tcp      
dockersymfony_elk_1           /usr/bin/supervisord -n -c ...   Up      0.0.0.0:81->80/tcp          
dockersymfony_nginx_1         nginx                            Up      443/tcp, 0.0.0.0:80->80/tcp
dockersymfony_php_1           php-fpm                          Up      0.0.0.0:9000->9000/tcp      
```

## Useful commands

```bash
# bash commands
$ docker-compose exec php bash

# Composer (e.g. composer update)
$ docker-compose exec php composer update

# F***ing cache/logs folder
$ sudo chmod -R 777 var/cache var/logs var/sessions # Symfony3

```

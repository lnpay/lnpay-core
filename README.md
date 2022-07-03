<a href="https://docs.lnpay.co/" rel="noopener" target="_blank"><img width="247" height="60" src="https://lnpay.co/frontend-resources/assets/logo_full.svg" alt="LNPAY"></a>

[![License](https://img.shields.io/badge/license-ELv2-yellow)](https://www.elastic.co/licensing/elastic-license)
![Build Status](https://github.com/lnpay/lnpay-core/actions/workflows/main.yml/badge.svg)
![Latest Stable Version](https://img.shields.io/github/tag/lnpay/lnpay-core.svg?label=stable)


#### LNPAY CORE is an enterprise toolkit / API for building Lightning Network applications on the web. Built with Yii2/PHP.


REQUIREMENTS
------------

Docker Engine is suggested for development environment. 
Since this is PHP, it's pretty easy to run on a base Ubuntu image with apache/nginx.


DEV ENVIRONMENT
------------

### Install with Docker

Clone repo

    $ git clone https://github.com/lnpay/lnpay-core

Run the build script

    $ cd lnpay-core
    $ bash docker.sh build
    
    # Wait for build process, then start the queue workers
    
    $ bash docker.sh start
    
Add the following line to your `/etc/hosts` file

    127.0.0.1 lnpay.local
    
You can then access the application through the following URL:

    http://lnpay.local:8111

CONFIGURATION
-------------

### .env file

There is a `.env.example` file that is automatically copied over as `.env` on first build.
This file contains the environment specific config vars. Here is an example

```
############## SAMPLE ENVIRONMENT DEFAULTS

INSTANCE_ID=lnpay-1

BASE_URL=https://lnpay.local:8111

# Yii Settings (IF DEVELOPMENT)
YII_DEBUG=true
YII_ENV=dev

# Database
DB_HOST=192.168.69.22
DB_USER=root
DB_PASS=example
DB_DB=lnpay_db

#PHP bin path
PHP_BIN_PATH=/usr/local/bin/php

# Redis
REDIS_HOST=192.168.69.44
REDIS_CACHE_DB=1
REDIS_MUTEX_DB=2

# Papertrail logging (OPTIONAL)
PAPERTRAIL_HOST=
PAPERTRAIL_PORT=

# cookie auth key, generate this randomly
YII_COOKIE_VALIDATION_KEY=609F45AEgenerateNEWinPRODEA480D3B68

#Supervisor Server, this is for monitoring RPC listeners
SUPERVISOR_RPC_HOST=192.168.69.11
SUPERVISOR_RPC_PORT=9001
SUPERVISOR_CONF_PATH=/app/docker/supervisor/conf.d/
SUPERVISOR_SERVER_APP_PATH=/app/

# Email - if DEV emails are sent to files in /runtime/mail
DEFAULT_EMAIL_FROM=
DEFAULT_EMAIL_HOST=
DEFAULT_EMAIL_PORT=
DEFAULT_EMAIL_USERNAME=
DEFAULT_EMAIL_PASSWORD=

# Helps track aggregate sat movement for an idea of what's going on (OPTIONAL)
AMPLITUDE_API_KEY=
```

# Operating the environment
   
### Starting / Stopping / Destroying Docker containers
    $ bash docker.sh start # Start docker container
    $ bash docker.sh stop # Stop docker container
    $ bash docker.sh destroy # Destroy docker containers

### Accessing docker containers
    $ docker exec -it lnpay-php bash
    $ docker exec -it lnpay-db bash

### Accessing local mysql database
   `lnpay.local:8222` with user/pass `root/example`
   
### Accessing local mysql database

Database migrations should be run often to pull in database changes from other devs.
      
      $ docker exec lnpay-php php yii migrate
    
**NOTES:** 
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches

WORKERS
-------------

`php yii queue/listen --verbose` to start a listener, and `php yii queue/run --verbose` can be used to run the queue and then exit.

The queue currently pulls from the `queue` table in the database.

http://lnpay.local:8111/monitor is a lightweight interface to see what is going on.



SUPERVISOR
----------
Supervisor is used to make sure workers stay up and keep RPC subscribes going

`$ supervisord`

Configuration files supervisor are mapped to the service inside the container for easy editing: `docker/dev/supervisor/`


TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](http://codeception.com/).
By default there are 4 test suites:

- `unit`
- `api`
- `functional`
- `acceptance`

Tests can be executed by running

```
docker exec lnpay-php vendor/bin/codecept run
```

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser. 

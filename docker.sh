#!/usr/bin/env bash

if [ $1 == "build" ]
then
    docker-compose up -d

    # Update
    docker exec lnpay-php apt-get update
    docker exec lnpay-php apt-get install -y libgmp-dev re2c libmhash-dev libmcrypt-dev file
    docker exec lnpay-php docker-php-ext-configure gmp
    docker exec lnpay-php docker-php-ext-install gmp

    docker exec lnpay-php composer install

    docker exec lnpay-php init --env=Development --overwrite=y

    # CRON
    docker exec lnpay-php crontab /app/docker/cron/lnpay.cron
    # docker exec lnpay-php "cron -f"

    # Restart after enable for apache
    docker restart lnpay-php

    docker exec lnpay-php php yii migrate --interactive=0 --migrationPath=@yii/rbac/migrations
    docker exec lnpay-php php yii migrate --interactive=0
    # docker exec lnpay-php supervisord

fi

if [ $1 == "start" ]
then
    docker-compose up -d
    docker exec lnpay-php pkill -f supervisord
    docker exec lnpay-php supervisord
fi

if [ $1 == "stop" ]
then
    set -e
    docker-compose stop
fi

if [ $1 == "destroy" ]
then
    set -e
    docker-compose down --volumes
    rm -rf docker/supervisor/conf.d/lnod_*
fi
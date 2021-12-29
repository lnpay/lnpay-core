#!/usr/bin/env bash

if [ $1 == "build" ]
then
    docker-compose up -d

    docker exec lnpay-php composer install

    docker exec lnpay-php init --env=Development --overwrite=y

    # CRON
    docker exec lnpay-php crontab /app/docker/cron/lnpay.cron
    # docker exec lnpay-php "cron -f"

    # Restart after enable for apache
    docker restart lnpay-php

    sleep 3

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

# THIS IS EXPERIMENTAL
if [ $1 == "polarup" ]
then
    cd tests/polar;
    set -e
    docker-compose up -d
    echo "Waiting 30s for bitcoind and LND to boot..."
    sleep 30
fi

# THIS IS EXPERIMENTAL
if [ $1 == "polardown" ]
then
    cd tests/polar;
    set -e
    docker-compose down --volumes
fi
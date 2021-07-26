#!/usr/bin/env bash

# Change BITNAMI HTDOCS dir to /web

rm -rf htdocs/index.html

git clone https://github.com/lnpay/lnpay htdocs

php init --env=Development --overwrite=y

# Change mysql password .env

composer install

php yii migrate --interactive=0 --migrationPath=@yii/rbac/migrations
php yii migrate --interactive=0


sudo apt-get update
sudo apt-get -y install libmcrypt-dev

sudo pecl install grpc
#!/usr/bin/env bash
set -x
if [ "$TRAVIS_PHP_VERSION" = 'hhvm' ] || [ "$TRAVIS_PHP_VERSION" = 'hhvm-nightly' ] ; then
    curl -sS https://getcomposer.org/installer > composer-installer.php
    hhvm composer-installer.php
    hhvm -v ResourceLimit.SocketDefaultTimeout=30 -v Http.SlowQueryThreshold=30000 composer.phar update
else
    composer self-update
    composer install
fi
#!/bin/bash

runuser -u horde -m -- /usr/local/bin/composer.phar -d /var/www/horde-dev install
echo "✅ Run composer installer to setup external dependencies."



#!/bin/bash
cd /usr/local/tools
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

gh release download --clobber -R horde/components -p 'horde-components.phar' -D ./tools/
gh release download --clobber -R phpstan/phpstan -p 'phpstan.phar' -D ./tools/
curl -o /usr/local/tools/phive.phar "https://phar.io/releases/phive.phar" 
curl -o /usr/local/tools/phpunit-12.phar https://phar.phpunit.de/phpunit-12.phar &> /dev/null
echo "✅ Downloaded composer, horde-components, phpstan and phive"
curl -o /usr/local/tools/phpunit-11.phar https://phar.phpunit.de/phpunit-11.phar &> /dev/null
curl -o /usr/local/tools/phpunit-10.phar https://phar.phpunit.de/phpunit-10.phar &> /dev/null
curl -o /usr/local/tools/phpunit-9.phar https://phar.phpunit.de/phpunit-9.phar   &> /dev/null
echo "✅ Downloaded phpunit 12, 11, 10 and 9"
chmod +x /usr/local/tools/*.phar
cp -rs /usr/local/tools/* /usr/local/bin &> /dev/null
echo "✅ Linked all phar tools to /usr/local/bin"

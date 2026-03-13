#!/bin/bash
cd /usr/local/tools
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# there was no 'latest' release entry so had to change it to a specific tag and file to make it work
gh release download v1.0.0alpha37 --clobber -R horde/components -p 'horde-components-1.0.0-alpha37.phar' -O /usr/local/tools/horde-components.phar
gh release download --clobber -R phpstan/phpstan -p 'phpstan.phar' -O /usr/local/tools/phpstan.phar
wget -O /usr/local/tools/phive.phar "https://phar.io/releases/phive.phar" 
wget -O /usr/local/tools/phpunit-12.phar https://phar.phpunit.de/phpunit-12.phar &> /dev/null
echo "✅ Downloaded composer, horde-components, phpstan and phive"
wget -O /usr/local/tools/phpunit-11.phar https://phar.phpunit.de/phpunit-11.phar &> /dev/null
wget -O /usr/local/tools/phpunit-10.phar https://phar.phpunit.de/phpunit-10.phar &> /dev/null
wget -O /usr/local/tools/phpunit-9.phar https://phar.phpunit.de/phpunit-9.phar   &> /dev/null
echo "✅ Downloaded phpunit 12, 11, 10 and 9"
chmod +x /usr/local/tools/*.phar
cp -rs /usr/local/tools/* /usr/local/bin &> /dev/null
echo "✅ Linked all phar tools to /usr/local/bin"

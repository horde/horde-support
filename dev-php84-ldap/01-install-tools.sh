#!/bin/bash
gh release download --clobber -R composer/composer -p 'composer.phar' -D ./tools/
gh release download --clobber -R horde/components -p 'horde-components.phar' -D ./tools/
gh release download --clobber -R phpstan/phpstan -p 'phpstan.phar' -D ./tools/
gh release download --clobber -R phar-io/phive -p 'phive*.phar' -D ./tools/
echo "✅ Downloaded composer, horde-components, phpstan and phive"
wget -O ./tools/phpunit-12.phar https://phar.phpunit.de/phpunit-12.phar &> /dev/null
wget -O ./tools/phpunit-11.phar https://phar.phpunit.de/phpunit-11.phar &> /dev/null
wget -O ./tools/phpunit-10.phar https://phar.phpunit.de/phpunit-10.phar &> /dev/null
wget -O ./tools/phpunit-9.phar https://phar.phpunit.de/phpunit-9.phar   &> /dev/null
echo "✅ Downloaded phpunit 12, 11, 10 and 9"
chmod +x ./tools/*.phar
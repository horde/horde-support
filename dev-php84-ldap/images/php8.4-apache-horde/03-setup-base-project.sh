#!/bin/bash
if [ -f $HORDE_INSTALL_DIR/.keep ]; then
    rm $HORDE_INSTALL_DIR/.keep
fi
runuser -l horde -w GITHUB_TOKEN,HORDE_INSTALL_DIR -c 'php /usr/local/tools/composer.phar create-project horde/bundle $HORDE_INSTALL_DIR'
runuser -l horde -w GITHUB_TOKEN,HORDE_INSTALL_DIR -c 'cd $HORDE_INSTALL_DIR && /usr/local/tools/horde-components.phar install'
echo "✅ Setup the local pseudo repository"


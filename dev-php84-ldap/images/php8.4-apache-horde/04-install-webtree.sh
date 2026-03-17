#!/bin/bash
runuser -l horde -w GITHBU_TOKEN,HORDE_INSTALL_DIR -c '/usr/local/tools/composer.phar -d $HORDE_INSTALL_DIR install'
echo "✅ Run composer installer to setup external dependencies."



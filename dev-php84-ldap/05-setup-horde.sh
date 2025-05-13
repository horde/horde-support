#!/bin/bash

## check if horde_apache container is running
if [ "$(docker ps -q -f name=horde_apache)" ]; then
    echo "✅ horde_apache container is running"
else
    echo "❌ horde_apache container is not running. Please start it first."
    exit 1
fi

docker exec -it horde_apache  sh -c "chmod +x /usr/local/tools/*" &> /dev/null
docker exec -it horde_apache  sh -c "cp -rs /usr/local/tools/* /usr/local/bin" &> /dev/null
echo "✅ Linked all phar tools to /usr/local/bin"
docker exec -e GITHUB_TOKEN=$GITHUB_TOKEN -u horde -it horde_apache /usr/local/bin/horde-components.phar github-clone-org
echo "✅ Cloned all horde components from github"
docker exec -e GITHUB_TOKEN=$GITHUB_TOKEN -u horde -it horde_apache /usr/local/bin/horde-components.phar install
echo "✅ Setup the local pseudo repository"
docker exec -e GITHUB_TOKEN=$GITHUB_TOKEN -u horde -w /var/www/horde-dev -it horde_apache composer.phar install
echo "✅ Run composer installer to setup external dependencies."



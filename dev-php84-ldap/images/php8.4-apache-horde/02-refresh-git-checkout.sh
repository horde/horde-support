#!/bin/bash
runuser -l horde -w GITHUB_TOKEN -c 'cd /srv/git && /usr/local/tools/horde-components.phar github-clone-org'
echo "✅ Cloned all horde components from github"


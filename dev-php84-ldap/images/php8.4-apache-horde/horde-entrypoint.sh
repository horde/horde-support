#!/bin/bash

# If a $GIT_CHECKOUT_UID variable is set, create a user with that UID
if [ -n "$GIT_CHECKOUT_UID" ]; then
    # Create a user with the specified UID
    useradd -d /srv/git -u $GIT_CHECKOUT_UID -M horde -s /bin/bash
    # Change ownership of the /var/www directory to the new user
    chown horde:www-data /srv/git
    chown -R horde:www-data /var/www
    echo "✅ Created user horde with UID $GIT_CHECKOUT_UID"
fi

## Only attempt if not present. Otherwise we try to autoupdate on every container restart and exhaust tokens.
if [ ! -f /usr/local/tools/horde-components.phar ]; then
  /usr/local/bin/01-install-tools.sh
fi

if [ ! -d /srv/git/horde ]; then
  /usr/local/bin/02-refresh-git-checkout.sh
fi

if [ ! -f /var/www/horde-dev/composer.json ]; then
  /usr/local/bin/03-setup-base-project.sh
fi

if [ ! -f /var/www/horde-dev/vendor/autoload.php ]; then
  /usr/local/bin/04-install-webtree.sh
fi

# Make any tools available in the PATH
chmod +x /usr/local/tools/*
cp -rs /usr/local/tools/* /usr/local/bin &> /dev/null
echo "✅ Linked all phar tools to /usr/local/bin"
# Delegate towards Apache
export APACHE_RUN_USER=horde
export APACHE_RUN_GROUP=horde
exec /usr/local/bin/apache2-foreground
echo "Why am I here?"

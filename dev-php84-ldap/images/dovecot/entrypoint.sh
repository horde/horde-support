#!/usr/bin/env bash

## Replace some well-known template variables in the horde config
if [[ -v EXPAND_CONFIGS ]]; then
    echo "EXPANDING CONFIG VARIABLES"
    if [[ -v MYSQL_DATABASE ]]; then
        echo "Setting env vars in /etc/dovecot/dovecot-sql.conf.ext"
        sed "s/connect = .*/connect = host=$MARIADB_HOSTNAME dbname=$MARIADB_DATABASE user=$MARIADB_USER password=$MARIADB_PASSWORD/g" /etc/dovecot/dovecot-sql.conf.ext > /etc/dovecot/tmp-sql.conf
        cp /etc/dovecot/tmp-sql.conf /etc/dovecot/dovecot-sql.conf.ext
        rm /etc/dovecot/tmp-sql.conf
    fi

    if [[ -v HORDE_DOMAIN ]]; then
        echo "Setting mail domain in /etc/dovecot/dovecot-sql.conf.ext"
        sed "s/horde.dev.local/$HORDE_DOMAIN/" \
            /etc/dovecot/dovecot-sql.conf.ext > \
            /etc/dovecot/tmp-sql.conf
        cp /etc/dovecot/tmp-sql.conf /etc/dovecot/dovecot-sql.conf.ext
        rm /etc/dovecot/tmp-sql.conf
    fi
fi
exec "$@"

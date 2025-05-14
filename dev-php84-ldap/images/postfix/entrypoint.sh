#!/usr/bin/env bash

## Replace some well-known template variables in the horde config
if [[ -v EXPAND_CONFIGS ]]; then
    echo "EXPANDING CONFIG VARIABLES"
    if [[ -v MYSQL_DATABASE ]]; then
        # fix permissions of volume-provided configs
        chown root:postfix /etc/postfix/mysql*

        # NOTE: sed -i does not work here and fails with "Device or resource busy..."

        # fill in the virtual_mailbox_domains config
        sed -e "s/user = postfix/user = $MARIADB_USER/" \
            -e "s/password = postfix/password = $MARIADB_PASSWORD/" \
            -e "s/hosts = localhost/hosts = $MARIADB_HOSTNAME/" \
            -e "s/dbname = postfix/dbname = $MARIADB_DATABASE/" \
            /etc/postfix/mysql_virtual_mailbox_domains.cf > \
            /etc/postfix/mysql_virtual_mailbox_domains.cf.new
        cp  /etc/postfix/mysql_virtual_mailbox_domains.cf.new \
            /etc/postfix/mysql_virtual_mailbox_domains.cf
        rm /etc/postfix/mysql_virtual_mailbox_domains.cf.new

        # fill in the virtual_mailbox_maps config
        sed -e "s/user = postfix/user = $MARIADB_USER/" \
            -e "s/password = postfix/password = $MARIADB_PASSWORD/" \
            -e "s/hosts = localhost/hosts = $MARIADB_HOSTNAME/" \
            -e "s/dbname = postfix/dbname = $MARIADB_DATABASE/" \
            /etc/postfix/mysql_virtual_mailbox_maps.cf > \
            /etc/postfix/mysql_virtual_mailbox_maps.cf.new
        cp /etc/postfix/mysql_virtual_mailbox_maps.cf.new \
            /etc/postfix/mysql_virtual_mailbox_maps.cf
        rm /etc/postfix/mysql_virtual_mailbox_maps.cf.new

        # add the virtual_mailbox_* configs to the main.cf
        postconf virtual_mailbox_domains=mysql:/etc/postfix/mysql_virtual_mailbox_domains.cf
        postconf virtual_mailbox_maps=mysql:/etc/postfix/mysql_virtual_mailbox_maps.cf

        # LMTP configuration
        postconf virtual_transport=lmtp:inet:${CONTAINER_PREFIX:-horde}_dovecot:24

        # submission port, smtp and sasl configuration
        postconf smtpd_tls_security_level=none
        postconf smtpd_sasl_auth_enable=yes
        postconf smtpd_sasl_type=dovecot
        postconf smtpd_sasl_path=inet:${CONTAINER_PREFIX:-horde}_dovecot:34343
        postconf smtpd_sasl_security_options=noanonymous
        postconf "smtpd_sasl_local_domain=\$myhostname"
        postconf smtpd_client_restrictions=permit_sasl_authenticated,reject
        #postconf smtpd_sender_login_maps=hash:/etc/postfix/virtual
        #postconf smtpd_sender_restrictions=reject_sender_login_mismatch
        postconf smtpd_recipient_restrictions=reject_non_fqdn_recipient,reject_unknown_recipient_domain,permit_sasl_authenticated,reject

        # enable submission port
        echo 'submission inet n       -       n       -       -       smtpd' >> /etc/postfix/master.cf

        # enable logging to stdout
        postconf maillog_file=/dev/stdout
    fi
fi
exec "$@"

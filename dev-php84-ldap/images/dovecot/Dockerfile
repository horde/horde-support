FROM docker.io/dovecot/dovecot:latest

COPY ./dovecot /etc/dovecot
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN ls -lah /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/bin/tini", "--", "/usr/local/bin/entrypoint.sh"]
CMD ["/usr/sbin/dovecot", "-F"]

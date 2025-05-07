FROM php:8.4-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/horde-dev/web/

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN apt-get update && apt-get install -y \
        libfreetype-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libldap2-dev \
        git gh \
        mc \
        vim \
        locales-all \
        libicu72 libicu-dev \
    && docker-php-ext-install gettext mysqli pdo pdo_mysql intl iconv ldap \
    && docker-php-ext-enable gettext mysqli pdo pdo_mysql intl iconv ldap \
    && rm -rf /var/lib/apt/lists/*

COPY --chmod=775 --chown=root *.sh /usr/local/bin/
ENTRYPOINT ["/usr/local/bin/horde-entrypoint.sh"]
CMD ["apache2-foreground"]

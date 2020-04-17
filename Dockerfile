FROM composer:latest
WORKDIR /opt/relay
ADD . /opt/relay/
RUN docker-php-ext-install pdo_mysql && composer install
ENTRYPOINT ["/opt/relay/docker-entrypoint.sh"]
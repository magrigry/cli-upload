# Dockerfile
FROM php:8.3-fpm-alpine

ARG IPE_GD_WITHOUTAVIF=1

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions bcmath gd gettext intl mcrypt mysqli opcache pcntl pdo_mysql pdo_pgsql soap sockets redis xsl zip

RUN apk --update add \
    supervisor \
    nginx &&\
    rm /var/cache/apk/*

COPY --chown=www-data:www-data . /var/www/html

RUN ls -la
RUN rm /var/www/html/.env

COPY stubs/nginx /etc/nginx
COPY stubs/php /usr/local/etc
COPY stubs/supervisor /etc/supervisor

RUN mkdir -p /var/run/php

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
RUN composer build-prod

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

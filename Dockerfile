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

COPY stubs/nginx /etc/nginx
COPY stubs/php /usr/local/etc
COPY stubs/supervisor /etc/supervisor

RUN mkdir -p /var/run/php

RUN php artisan config:cache

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

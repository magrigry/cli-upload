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

COPY --chown=www-data:www-data ./app /var/www/html/app
COPY --chown=www-data:www-data ./bootstrap/app.php /var/www/html/bootstrap/app.php
COPY --chown=www-data:www-data ./bootstrap/providers.php /var/www/html/bootstrap/providers.php
COPY --chown=www-data:www-data ./config /var/www/html/config
COPY --chown=www-data:www-data ./database/migrations /var/www/html/database/migrations
COPY --chown=www-data:www-data ./database/schema /var/www/html/database/schema
COPY --chown=www-data:www-data ./public /var/www/html/public
COPY --chown=www-data:www-data ./resources /var/www/html/resources
COPY --chown=www-data:www-data ./routes /var/www/html/routes
COPY --chown=www-data:www-data ./tests /var/www/html/tests
COPY --chown=www-data:www-data ./vendor /var/www/html/vendor
COPY --chown=www-data:www-data ./storage /var/www/html/storage
COPY --chown=www-data:www-data \
        ./artisan \
        ./composer.json \
        ./composer.lock \
        ./package.json \
        ./package-lock.json \
        ./vite.config.js \
         /var/www/html/

RUN mkdir /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 755 /var/www/html/bootstrap/cache

RUN mkdir -p /var/www/html/storage/app/private
RUN mkdir -p /var/www/html/storage/app/public
RUN mkdir -p /var/www/html/storage/app/framework/cache
RUN mkdir -p /var/www/html/storage/app/framework/session
RUN mkdir -p /var/www/html/storage/app/framework/testing
RUN mkdir -p /var/www/html/storage/app/framework/views
RUN mkdir -p /var/www/html/storage/app/framework/logs

RUN chown www-data:www-data -R /var/www/html/storage
RUN chmod 755 -R /var/www/html/storage

COPY stubs/nginx /etc/nginx
COPY stubs/php /usr/local/etc
COPY stubs/supervisor /etc/supervisor

RUN mkdir -p /var/run/php

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
RUN composer build-prod

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

FROM php:8.1-apache

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

RUN pecl -q install xdebug-3.1.6 \
    && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


COPY 000-default.conf /etc/apache2/sites-available
COPY default-ssl.conf /etc/apache2/sites-available

WORKDIR /var/www

EXPOSE 8080/tcp
EXPOSE 8443/tcp

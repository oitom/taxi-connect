FROM php:8.0-apache

ENV XDEBUG_MODE=coverage

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer require --dev phpunit/php-code-coverage
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN a2enmod rewrite
WORKDIR /var/www/html

COPY . .

COPY composer.json /var/www/html/
RUN composer install --dev
RUN chmod 777 /var/www/html/data

CMD ["apache2-foreground"]
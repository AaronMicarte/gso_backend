FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY . /var/www/html/

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

RUN sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
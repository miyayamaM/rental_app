FROM php:8.0-apache
COPY ./app /var/www/app
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y \
    git \
    zlib1g-dev \
    libzip-dev \
    zip \
    unzip \
    vim \
    && docker-php-ext-install pdo_mysql \
    zip

RUN sed -i 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

RUN cd /etc/apache2/mods-enabled \
&& ln -s ../mods-available/rewrite.load
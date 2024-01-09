FROM php:7.4-fpm

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Configuration de php
ADD php.ini /usr/local/etc/php/conf.d/

# Packages list update
RUN apt-get update

# Packages installation
RUN apt-get install -y libjpeg-dev libpng-dev libzip-dev libfreetype-dev unzip less git

# Lib GD requirement
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg

# PHP packages installation
RUN docker-php-ext-install gd

# PHP exif mbstring extensions installation
#RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/htdocs

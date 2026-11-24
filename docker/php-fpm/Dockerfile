FROM php:8.5-fpm

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions pdo_mysql intl zip gd exif bcmath opcache

RUN printf "upload_max_filesize=20M\npost_max_size=25M\nmemory_limit=256M\n" > /usr/local/etc/php/conf.d/namaqu.ini

RUN mkdir -p /var/www/api-root && ln -s /var/www/namaqu/backend/public /var/www/api-root/namaqu
WORKDIR /var/www/namaqu/backend

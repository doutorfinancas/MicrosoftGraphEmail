FROM php:7.2.34-fpm

RUN apt-get update && \
    curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin --filename=composer && chmod +x /usr/bin/composer && \
    apt-get install -y \
    zip
FROM php:7.2.34-fpm

SHELL ["/bin/bash", "-o", "pipefail", "-c"]

RUN apt-get update && \
    apt-get install --no-install-recommends -y \
    zip \
    && curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin --filename=composer  \
    && chmod +x /usr/bin/composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

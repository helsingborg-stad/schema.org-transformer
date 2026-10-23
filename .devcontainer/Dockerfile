# The official devcontainer PHP image lags behind on patch releases, so PHP itself
# is taken from the official PHP image to pin the exact version.
FROM php:8.4.27-cli-bookworm AS php

FROM mcr.microsoft.com/devcontainers/php:4.1.5-8.4-bookworm

# Overlay the pinned PHP build. Both images share the Debian base and PHP API,
# so extensions shipped with the devcontainer image (e.g. xdebug) keep working.
COPY --from=php /usr/local/bin/ /usr/local/bin/
COPY --from=php /usr/local/lib/php/ /usr/local/lib/php/
COPY --from=php /usr/local/include/php/ /usr/local/include/php/
COPY --from=php /usr/local/etc/php/ /usr/local/etc/php/
COPY --from=php /usr/src/php.tar.xz /usr/src/php.tar.xz.asc /usr/src/

ENV PHP_VERSION=8.4.27 \
    PHP_URL=https://www.php.net/distributions/php-8.4.27.tar.xz \
    PHP_ASC_URL=https://www.php.net/distributions/php-8.4.27.tar.xz.asc \
    PHP_SHA256=dcf93923b0e38e0d5252b25b796520b5a47761a08b3488b6d0b5d1115764d3f0

RUN apt-get update && apt-get install -y --no-install-recommends \
    cron \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install intl zip \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

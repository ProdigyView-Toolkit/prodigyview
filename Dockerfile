FROM php:8.5-fpm

WORKDIR /code

# The database test matrix exercises MySQL, PostgreSQL, SQLite, and MongoDB.
# SQLite ships in the PHP 8.5 FPM image; the other database extensions are
# compiled here so applications and tests share the same runtime.
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libssl-dev \
        pkg-config \
        ${PHPIZE_DEPS} \
    && docker-php-ext-install \
        mysqli \
        pdo_mysql \
        pgsql \
        pdo_pgsql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Use the maintained Composer image instead of downloading the installer in
# this Dockerfile. That keeps the build repeatable while tracking Composer 2.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

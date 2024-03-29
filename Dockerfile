FROM php:8.1.0-fpm

RUN apt-get update -y && apt-get upgrade  -y

RUN apt-get install git -y

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer
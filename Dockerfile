FROM php:8.2-cli

ENV UNAME=pcom
ARG GID
ARG UID

RUN apt-get update && apt-get install -y libzip-dev zip && docker-php-ext-install zip
RUN pecl install xdebug-3.2.1 && docker-php-ext-enable xdebug
RUN groupadd -g $GID -o $UNAME
RUN useradd -m -u $UID -g $GID -o -s /bin/bash $UNAME

USER $UNAME

WORKDIR /usr/src/php-compiler

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

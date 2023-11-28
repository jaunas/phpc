FROM php:8.2-cli

ARG UNAME
ARG GID
ARG UID

RUN apt-get update && apt-get install --no-install-recommends -y libzip-dev zip unzip && apt-get clean
RUN docker-php-ext-install zip
RUN pecl install xdebug-3.2.1 && docker-php-ext-enable xdebug
RUN groupadd -g ${GID} -o ${UNAME}
RUN useradd -m -u ${UID} -g ${GID} -o -s /bin/bash ${UNAME}

USER $UNAME

RUN curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y
ENV PATH="/home/${UNAME}/.cargo/bin:${PATH}"

WORKDIR /usr/src/phpc

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ARG PHP_VERSION=7.4
FROM php:${PHP_VERSION}-cli

RUN echo "Europe/Zurich" > /etc/timezone; dpkg-reconfigure tzdata

# Install/configure PHP extensions
COPY docker/php/extensions.sh /usr/local/bin/
RUN /usr/local/bin/extensions.sh

# Use the default development PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install Xdebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

# Install Composer
COPY docker/php/composer.sh /usr/local/bin/
RUN /usr/local/bin/composer.sh

WORKDIR /app

ENTRYPOINT [ "composer" ]

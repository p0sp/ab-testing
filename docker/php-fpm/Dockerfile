FROM composer:latest AS composer_data
#ARG composer_additional_flags=--no-dev
WORKDIR /app
COPY composer.lock composer.json /app/
#RUN composer install --ignore-platform-reqs ${composer_additional_flags} --no-interaction --no-scripts --prefer-dist --optimize-autoloader
#RUN composer install --ignore-platform-reqs  --no-interaction --no-scripts --prefer-dist --optimize-autoloader

FROM php:7.3-fpm
ARG ENVIROMENT=prod
RUN apt-get update \
    && apt-get install -y \
        zip \
        unzip \
        bzip2 \
        git \
        procps \
    && apt-get clean -y

# Install XDebug
RUN if [ "$ENVIROMENT" = "dev" ]; \
        then apt-get update && pecl install xdebug && docker-php-ext-enable xdebug; \
    fi

# Configure XDebug
RUN if [ "$ENVIROMENT" = "dev" ]; \
        then \
        { \
            echo "xdebug.remote_enable=1"; \
            echo "xdebug.remote_handler=dbgp"; \
            echo "xdebug.default_enable=1"; \
            echo "xdebug.remote_autostart=1"; \
            echo "xdebug.remote_port=9001"; \
            echo "xdebug.remote_connect_back=1"; \
            echo "xdebug.profiler_enable=1"; \
            echo "xdebug.remote_host=172.17.0.1"; \
            echo "xdebug.idekey=PHPSTORM"; \
        } | tee -a /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        fi

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && composer selfupdate -q

WORKDIR /code
COPY --from=composer_data /app /code
RUN chown -R www-data:www-data /code
COPY . /code
COPY ./docker/php-fpm/files /startup
RUN chmod +x -R /startup

ENTRYPOINT ["/startup/entrypoint.sh"]

HEALTHCHECK --interval=5m --timeout=3s \
  CMD /code/bin/console || exit 1


EXPOSE 9000
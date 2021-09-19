FROM php:7.4-fpm-alpine

# timezone environment
ENV TZ=UTC \
  # locale
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

ENV IMAGE_LIBRARY \
    libjpeg-turbo-dev freetype-dev libpng-dev 

RUN apk add --no-cache \
    git \
    bash \
    curl \
    curl-dev \
    oniguruma-dev \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    openssl \
    openssl-dev

# installed: pdo, pdo_sqlite, openssl, ctype, curl, mbstring, xml
RUN docker-php-ext-install -j$(nproc) \
      pdo_mysql \
      bcmath \
      opcache \
      intl \
      zip

RUN apk add --no-cache autoconf gcc g++ make imagemagick-dev ${IMAGE_LIBRARY} \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd exif \
    && pecl install imagick \
    && docker-php-ext-enable imagick

RUN apk add --no-cache pcre-dev libmemcached-dev re2c zlib-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && pecl install psr \
    && docker-php-ext-enable psr \
    && pecl clear-cache

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN composer config -g process-timeout 3600 && \
    composer config -g repos.packagist composer https://packagist.org

RUN composer global require phalcon/zephir

WORKDIR /usr/local/src

# Phalconをgitからインストールしてビルドする。
RUN git clone git://github.com/phalcon/cphalcon.git && \
    cd cphalcon                                     && \
    git checkout tags/v4.1.2                        && \
    ${COMPOSER_HOME}/vendor/bin/zephir fullclean                                && \
    ${COMPOSER_HOME}/vendor/bin/zephir build

WORKDIR /usr/local/src/cphalcon/

# 先ほど入れたPhalconをPHP extensionとして追加する
RUN ${COMPOSER_HOME}/vendor/bin/zephir fullclean            && \
    ${COMPOSER_HOME}/vendor/bin/zephir compile              && \
    cd ext                      && \
    phpize                      && \
    ./configure                 && \
    make                        && \
    make install



COPY ./infra/docker/php/php-fpm.d/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./infra/docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./infra/docker/php/extension.ini /usr/local/etc/php/conf.d/extension.ini

WORKDIR /work/backend

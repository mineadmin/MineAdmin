# Default Dockerfile

ARG ALPINE_VERSION=3.15

FROM alpine:$ALPINE_VERSION

LABEL maintainer="MineManage Developers <group@stye.cn>" version="1.0" license="MIT" app.name="MineManage"

ARG ALPINE_VERSION=3.15

# trust this project public key to trust the packages.
ADD https://php.hernandev.com/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

##
# ---------- building ----------
##
RUN set -ex \
    # change apk source repo
    && echo "https://php.hernandev.com/v$ALPINE_VERSION/php-8.1" >> /etc/apk/repositories \
    && echo "@php https://php.hernandev.com/v$ALPINE_VERSION/php-8.1" >> /etc/apk/repositories \
    && apk update \
    && apk add --no-cache \
    # Install base packages ('ca-certificates' will install 'nghttp2-libs')
    ca-certificates \
    curl \
    wget \
    tar \
    xz \
    libressl \
    tzdata \
    pcre \
    php8 \
    php8-bcmath \
    php8-curl \
    php8-ctype \
    php8-dom \
    php8-gd \
    php8-iconv \
    php8-mbstring \
    php8-mysqlnd \
    php8-openssl \
    php8-pdo \
    php8-pdo_mysql \
    php8-pdo_sqlite \
    php8-phar \
    php8-posix \
    php8-redis \
    php8-sockets \
    php8-sodium \
    php8-sysvshm \
    php8-sysvmsg \
    php8-sysvsem \
    php8-zip \
    php8-zlib \
    php8-xml \
    php8-xmlreader \
    php8-pcntl \
    php8-opcache \
    && ln -sf /usr/bin/php8 /usr/bin/php \
    && apk del --purge *-dev \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/share/php8 \
    && php -v \
    && php -m \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

ARG COMPOSER_VERSION=2.3.10

# update
RUN set -ex \
    && apk update \
    # for extension libaio linux-headers
    && apk add --no-cache libstdc++ openssl git bash  php8-pear php8-dev autoconf pcre2-dev c-ares-dev zlib-dev re2c gcc g++ make \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS libaio-dev openssl-dev curl-dev  \
    # php extension:swoole
    && ln -s /usr/bin/pecl8 /usr/local/bin/pecl \
    && pecl channel-update pecl.php.net \
    && pecl install --configureoptions 'enable-sockets="no" enable-openssl="yes" enable-http2="yes" enable-mysqlnd="no" enable-swoole-json="yes" enable-swoole-curl="yes" enable-cares="no"' swoole \
    && {\
    echo "memory_limit=1G"; \
    echo "upload_max_filesize=128M"; \
    echo "post_max_size=128M"; \
    echo "memory_limit=1G"; \
    echo "date.timezone=Asia/Shanghai"; \
    } | tee /etc/php8/conf.d/00_default.ini \
    && echo "opcache.enable_cli = 'On'" >> /etc/php8/conf.d/00_opcache.ini \
    &&{ \
    echo "extension=swoole.so";\
    echo "swoole.use_shortname = 'Off'";\
    } | tee  /etc/php8/conf.d/50_swoole.ini \
    # install composer
    && wget -nv -O /usr/local/bin/composer https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar \
    && chmod u+x /usr/local/bin/composer \
    # php info
    && php -v \
    && php -m \
    && php --ri swoole \
    && php --ri Zend\ OPcache \
    && composer --version \
    # ---------- clear works ----------
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/local/bin/php* \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"} \
    #    APP_ENV=dev \
    APP_SYSTEM_ENV=docker \
    SCAN_CACHEABLE=(true)

# update
RUN set -ex \
    #  ---------- some config ----------
    && cd /etc/php8 \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

RUN set -ex apk update \
    && apk add --no-cache libstdc++ openssl git bash autoconf pcre2-dev zlib-dev re2c gcc g++ make \
    php8-pear php8-dev php8-tokenizer php8-fileinfo php8-simplexml php8-xmlwriter \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS zlib-dev libaio-dev openssl-dev curl-dev  c-ares-dev \
    && pecl channel-update pecl.php.net \
    && ln -s /usr/bin/phpize8 /usr/local/bin/phpize \
    && ln -s /usr/bin/php-config8 /usr/local/bin/php-config \
    && pecl install --configureoptions 'enable-reader="yes"' xlswriter \
    &&  echo "extension=xlswriter.so" >> /etc/php8/conf.d/60-xlswriter.ini \
    && php -m \
    && php -v \
    && php --ri swoole \
    && mkdir -p /app-src \
    # ---------- clear works ----------
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/local/bin/php* \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

# fix aliyun oss wrong charset: https://github.com/aliyun/aliyun-oss-php-sdk/issues/101
# https://github.com/docker-library/php/issues/240#issuecomment-762438977

RUN apk --no-cache --allow-untrusted --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ add gnu-libiconv gnu-libiconv-dev \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/local/bin/php* \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

WORKDIR /app-src

EXPOSE 9501 9502 9503

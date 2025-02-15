FROM hyperf/hyperf:8.1-alpine-v3.18-swoole
LABEL maintainer="MineManage Developers <group@stye.cn>" version="1.0" license="MIT" app.name="MineManage"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

# update
RUN set -ex \
    #  ---------- some config ----------
    && cd /etc/php81 \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

RUN set -ex && \
    apk update \
    && apk add --no-cache libstdc++ openssl git bash autoconf pcre2-dev zlib-dev re2c gcc g++ make \
    php81-pear php81-dev php81-tokenizer php81-fileinfo php81-simplexml php81-xmlwriter \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS zlib-dev libaio-dev openssl-dev curl-dev  c-ares-dev \
    && pecl channel-update pecl.php.net \
    && pecl install --configureoptions 'enable-reader="yes"' xlswriter \
    && echo "extension=xlswriter.so" >> /etc/php81/conf.d/60-xlswriter.ini \
    && php -m \
    && php -v \
    && php --ri swoole \
    && mkdir -p /app-src \
    # ---------- clear works ----------
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/local/bin/php* \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

WORKDIR /opt/www

COPY . /opt/www

RUN composer install --no-dev -o && cp .env.example .env && php bin/hyperf.php

EXPOSE 9501 9502 9503

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]

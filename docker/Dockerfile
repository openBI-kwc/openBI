FROM php:7.3-fpm-alpine

ENV TIMEZONE Asia/Shanghai

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN  sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

#基础依赖
RUN apk update \
    && apk add --no-cache --virtual .build-deps \
    autoconf \
    build-base \
    binutils \
    musl \
    linux-headers &&\
    apk add tzdata runit nginx freetype-dev gd-dev libjpeg-turbo-dev libpng-dev libmcrypt-dev libevent-dev bash libzip-dev gnu-libiconv openldap-dev  && \
    cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
    echo "${TIMEZONE}" > /etc/timezone && \
    sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /usr/local/etc/php-fpm.conf && \
    sed -i -e "s/listen\s*=\s*127.0.0.1:9000/listen = 9000/g" /usr/local/etc/php-fpm.d/www.conf && \
    docker-php-ext-configure gd --with-freetype-dir --with-jpeg-dir &&\
    docker-php-ext-install -j$(nproc) gd &&\
    docker-php-ext-install pdo_mysql mysqli bcmath sockets opcache zip ldap && pecl channel-update pecl.php.net && pecl install seaslog redis xdebug &&\
    docker-php-ext-enable redis opcache && pecl clear-cache &&\
    echo -e "opcache.enable=1\nopcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini &&\
    mkdir -p /run/nginx \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

ADD ./php.ini /usr/local/etc/php/php.ini
ADD ./nginx.service /etc/service/nginx/run
RUN chmod a+x /etc/service/nginx/run
ADD ./fpm.service /etc/service/fpm/run
RUN chmod a+x /etc/service/fpm/run
ADD ./nginx.conf /etc/nginx/nginx.conf
ADD ./default.conf /etc/nginx/conf.d/default.conf
RUN chmod a+x /usr/bin/composer

WORKDIR /data

EXPOSE 80
CMD ["runsvdir", "/etc/service"]
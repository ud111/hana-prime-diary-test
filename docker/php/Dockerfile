# PHP 8.5 (最新リリース) + php-fpm。Laravel 13 の実行に必要な拡張と Composer を同梱
FROM php:8.5.10-fpm-alpine

ARG UID=1000
ARG GID=1000

# Laravel / Composer に必要なツール・拡張
# PHP 8.5 では OPcache が本体に組み込まれているため docker-php-ext-install opcache は不要
# (実行するとビルドが失敗する)
# GD は画像の軽量化 (WebP / AVIF への変換とサムネイル生成) に使う。ビルド用の -dev パッケージは入れた後に外す
RUN apk add --no-cache git unzip libjpeg-turbo libwebp libavif libpng \
    && apk add --no-cache --virtual .build-deps libjpeg-turbo-dev libwebp-dev libavif-dev libpng-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-avif \
    && docker-php-ext-install pdo_mysql gd \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini

# ホストの UID/GID と揃えた非 root ユーザーで php-fpm とコマンドを実行する
# (bind mount したソースの所有権問題を避ける)
RUN addgroup -g "${GID}" app \
    && adduser -D -u "${UID}" -G app app \
    && sed -i -e '/^user = /d' -e '/^group = /d' -e '/^listen\.owner/d' -e '/^listen\.group/d' \
        /usr/local/etc/php-fpm.d/www.conf \
    && chown -R app:app /var/www

WORKDIR /var/www/html
USER app

EXPOSE 9000
CMD ["php-fpm"]

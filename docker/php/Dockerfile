# PHP 8.5 (最新リリース) + php-fpm。Laravel 13 の実行に必要な拡張と Composer を同梱
FROM php:8.5.10-fpm-alpine

ARG UID=1000
ARG GID=1000

# Laravel / Composer に必要なツール・拡張
RUN apk add --no-cache git unzip \
    && docker-php-ext-install pdo_mysql

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

# BaseImage
FROM php:8.1-apache
COPY php.ini /usr/local/etc/php/

# package update & install
# Dockerは文を実行するたびにイメージのレイヤーを作成されてしまうので ; でワンライナーにする \ で次行に続く
RUN apt-get update && apt-get install -y locales zlib1g-dev libzip-dev mysql-common wget npm \
  && docker-php-ext-install pdo_mysql zip \
  && locale-gen ja_JP.UTF-8

ENV LANG ja_JP.UTF-8
ENV LC_CTYPE ja_JP.UTF-8
RUN localedef -f UTF-8 -i ja_JP ja_JP.utf8
ENV LC_ALL ja_JP.UTF-8

# Composer install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer self-update --2
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

WORKDIR /var/www/html

# ターミナル画面を見やすく変更
RUN echo 'export PS1="\n\[\033[35m\]\u@\h\[\033[00m\]:\[\033[36m\]\w\[\033[00m\]\n[app] \$ "' >> /root/.bashrc

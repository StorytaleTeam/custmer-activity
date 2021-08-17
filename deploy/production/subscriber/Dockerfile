FROM php:7.4-fpm
ENV TZ=Europe/Moscow
RUN apt update && apt install -y \
    libbz2-dev \
    libxml2-dev \
    libpng-dev \
    libpq-dev \
    libssl-dev \
    librabbitmq-dev \
    libmemcached-dev \
    libfcgi-bin \
    zlib1g-dev \
    libzip-dev \
    git \
    supervisor \
    vim \
    nano
RUN docker-php-ext-install -j$(nproc) \
    bz2 \
    xml \
    iconv \
    pgsql \
    pdo \
    pdo_pgsql \
    exif \
    gettext \
    sockets \
    opcache \
    intl \
    gd \
    zip
RUN pecl channel-update pecl.php.net \
    && apt install -y libmagickwand-dev --no-install-recommends \
    && pecl install amqp memcached apcu imagick\
    && docker-php-ext-enable amqp memcached apcu imagick
COPY --chown=33:33 . /www
COPY subscriber/php.ini /usr/local/etc/php/
COPY subscriber/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY subscriber/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord"]

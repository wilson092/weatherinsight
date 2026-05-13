FROM php:8.3-fpm

# Set Environment Variables
ENV PS1="\u@\h:\w\\$ "
ENV TZ="Asia/Jakarta"
ENV COMPOSER_MEMORY_LIMIT='-1'

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libmemcached-dev iputils-ping telnet netcat-openbsd \
    libmcrypt-dev libreadline-dev libgmp-dev libzip-dev \
    libpq-dev libjpeg-dev libpng-dev libfreetype6-dev \
    libssl-dev openssh-server libmagickwand-dev git cron \
    nano libxml2-dev nodejs npm && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install soap exif pcntl intl gmp zip pdo_mysql pdo_pgsql bcmath gd && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd

# Install PECL extensions
RUN pecl install redis mongodb-1.16.0 imagick xdebug memcached && \
    docker-php-ext-enable redis mongodb imagick xdebug memcached

# Install Composer
RUN curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Laravel Scheduler (Cron job)
RUN echo "* * * * * root /usr/local/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-scheduler && \
    chmod 0644 /etc/cron.d/laravel-scheduler

# Set working directory
WORKDIR /var/www/html

# Copy custom PHP configuration
ADD ./local.ini /usr/local/etc/php/conf.d

# Aliases for common Laravel commands
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan "$@"' > /usr/bin/art && \
    chmod +x /usr/bin/art && \
    echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan migrate "$@"' > /usr/bin/migrate && \
    chmod +x /usr/bin/migrate && \
    echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan migrate:fresh --seed' > /usr/bin/fresh && \
    chmod +x /usr/bin/fresh && \
    echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan config:clear\n/var/www/html/vendor/bin/phpunit -d memory_limit=2G --stop-on-error --stop-on-failure --testdox-text=tests/report.txt "$@"' > /usr/bin/t && \
    chmod +x /usr/bin/t && \
    echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan config:clear\n/usr/local/bin/php /var/www/html/artisan dusk -d memory_limit=2G --stop-on-error --stop-on-failure --testdox-text=tests/report-dusk.txt "$@"' > /usr/bin/d && \
    chmod +x /usr/bin/d

# Copy entrypoint script
COPY ./docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    ln -s /usr/local/bin/docker-entrypoint.sh /

# Set entrypoint and expose port
ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 9000

# Default command
CMD ["php-fpm"]

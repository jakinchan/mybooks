FROM wordpress:php8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl less unzip vim \
    && docker-php-ext-install pdo_sqlite sqlite3 \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp

RUN mkdir -p /var/www/sqlite /var/www/html/wp-content/uploads \
    && chown -R www-data:www-data /var/www/sqlite /var/www/html/wp-content/uploads

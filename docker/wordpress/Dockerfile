FROM wordpress:php8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl less unzip vim \
    && rm -rf /var/lib/apt/lists/*

RUN php -m | grep -qi '^pdo_sqlite$' \
    && php -m | grep -qi '^sqlite3$'

RUN curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp

RUN curl -fsSL https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip -o /tmp/sqlite-database-integration.zip \
    && unzip -q /tmp/sqlite-database-integration.zip -d /usr/src/wordpress/wp-content/plugins \
    && cp /usr/src/wordpress/wp-content/plugins/sqlite-database-integration/db.copy /usr/src/wordpress/wp-content/db.php \
    && rm /tmp/sqlite-database-integration.zip

RUN curl -fsSL https://downloads.wordpress.org/plugin/free-pdf-to-flipbook.latest-stable.zip -o /tmp/free-pdf-to-flipbook.zip \
    && unzip -q /tmp/free-pdf-to-flipbook.zip -d /usr/src/wordpress/wp-content/plugins \
    && rm /tmp/free-pdf-to-flipbook.zip

RUN mkdir -p /var/www/sqlite /var/www/html/wp-content/uploads \
    && chown -R www-data:www-data /var/www/sqlite /var/www/html/wp-content/uploads

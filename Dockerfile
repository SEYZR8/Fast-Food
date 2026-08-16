FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY . /var/www/html/

RUN set -eux; \
    chown -R www-data:www-data /var/www/html; \
    find /var/www/html -type d -exec chmod 755 {} \;; \
    find /var/www/html -type f -exec chmod 644 {} \;; \
    mkdir -p /var/www/html/css /var/www/html/js /var/www/html/images /var/www/html/admin/css /var/www/html/database/js /var/www/html/database/upload; \
    for f in /var/www/html/*.css; do [ -f "$f" ] && ln -sf "../$(basename "$f")" "/var/www/html/css/$(basename "$f")" || true; done; \
    for f in /var/www/html/*.js; do [ -f "$f" ] && ln -sf "../$(basename "$f")" "/var/www/html/js/$(basename "$f")" || true; done; \
    for f in /var/www/html/*.{png,jpg,jpeg,gif,webp}; do [ -f "$f" ] && ln -sf "../$(basename "$f")" "/var/www/html/images/$(basename "$f")" || true; done; \
    ln -sf ../../-variables.css /var/www/html/admin/css/-variables.css; \
    ln -sf ../../-global.css /var/www/html/admin/css/-global.css; \
    for f in /var/www/html/*.php; do b=$(basename "$f"); [ "$b" != "conf.php" ] && ln -sf "../$b" "/var/www/html/database/$b" || true; done; \
    ln -sf ../../ajax.js /var/www/html/database/js/ajax.js; \
    chown -R www-data:www-data /var/www/html/database /var/www/html/css /var/www/html/js /var/www/html/images /var/www/html/admin

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["sh", "-c", "if [ -n \"$PORT\" ] && [ \"$PORT\" != \"10000\" ]; then sed -i \"s/Listen 10000/Listen $PORT/\" /etc/apache2/ports.conf; sed -i \"s/<VirtualHost \\*:10000>/<VirtualHost *:$PORT>/\" /etc/apache2/sites-available/000-default.conf; fi; apache2-foreground"]

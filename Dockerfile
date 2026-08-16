FROM php:8.2-apache

RUN docker-php-ext-install mysqli     && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

COPY docker/apache-render.conf /etc/apache2/sites-available/000-default.conf
COPY docker/start.sh /usr/local/bin/render-start

RUN chmod +x /usr/local/bin/render-start

EXPOSE 10000

CMD ["render-start"]

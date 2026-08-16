FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["sh", "-c", "if [ -n \"$PORT\" ] && [ \"$PORT\" != \"10000\" ]; then sed -i \"s/Listen 10000/Listen $PORT/\" /etc/apache2/ports.conf; sed -i \"s/<VirtualHost \\*:10000>/<VirtualHost *:$PORT>/\" /etc/apache2/sites-available/000-default.conf; fi; apache2-foreground"]

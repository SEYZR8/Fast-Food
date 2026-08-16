FROM php:8.2-apache

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server mariadb-client \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/

RUN mkdir -p /var/www/html/images /var/www/html/css /var/www/html/js /var/www/html/database/upload /var/www/html/admin/css /var/www/html/admin/js \
    && find /var/www/html -maxdepth 1 -type f \( -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.gif' -o -iname '*.webp' \) -exec cp -n {} /var/www/html/images/ \; \
    && find /var/www/html -maxdepth 1 -type f -name '*.css' -exec cp -f {} /var/www/html/css/ \; \
    && find /var/www/html -maxdepth 1 -type f -name '*.js' -exec cp -f {} /var/www/html/js/ \; \
    && if [ -f /var/www/html/-variables.css ]; then cp -f /var/www/html/-variables.css /var/www/html/admin/css/-variables.css; fi \
    && if [ -f /var/www/html/-global.css ]; then cp -f /var/www/html/-global.css /var/www/html/admin/css/-global.css; fi \
    && for f in DirectOrder.php Product.php add_to_whitelist.php banner.php cart.php directOrder2.php invoice.php jsonFile.php logout.php orderFormHtml.php search.php sign_in.php sign_up.php user_check_login.php weeklyProduct.php; do if [ -f "/var/www/html/$f" ]; then cp -f "/var/www/html/$f" /var/www/html/database/; fi; done \
    && for f in function.php t.php money.php; do if [ -f "/var/www/html/$f" ]; then cp -f "/var/www/html/$f" "/var/www/html/admin/$f"; fi; done \
    && find /var/www/html -maxdepth 1 -type f \( -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.gif' -o -iname '*.webp' \) -exec cp -n {} /var/www/html/database/upload/ \; \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 10000
COPY docker/start.sh /usr/local/bin/render-start
RUN chmod +x /usr/local/bin/render-start
CMD ["/usr/local/bin/render-start"]

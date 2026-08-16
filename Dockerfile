FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY . /var/www/html/

# The original project uses folders such as images/, css/, js/, database/ and admin/.
# The GitHub repository was uploaded flattened, so rebuild that layout at image build time.
RUN mkdir -p /var/www/html/images /var/www/html/css /var/www/html/js \
    /var/www/html/database/upload /var/www/html/admin/css \
    /var/www/html/admin/js \
    && find /var/www/html -maxdepth 1 -type f \( -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.gif' -o -iname '*.webp' \) -exec cp -n {} /var/www/html/images/ \; \
    && find /var/www/html -maxdepth 1 -type f -name '*.css' -exec cp -f {} /var/www/html/css/ \; \
    && find /var/www/html -maxdepth 1 -type f -name '*.js' -exec cp -f {} /var/www/html/js/ \; \
    && if [ -f /var/www/html/-variables.css ]; then cp -f /var/www/html/-variables.css /var/www/html/admin/css/-variables.css; fi \
    && if [ -f /var/www/html/-global.css ]; then cp -f /var/www/html/-global.css /var/www/html/admin/css/-global.css; fi \
    && for f in DirectOrder.php Product.php add_to_whitelist.php banner.php cart.php directOrder2.php invoice.php jsonFile.php logout.php orderFormHtml.php search.php sign_in.php sign_up.php user_check_login.php weeklyProduct.php; do \
         if [ -f "/var/www/html/$f" ]; then cp -f "/var/www/html/$f" /var/www/html/database/; fi; \
       done \
    && for f in function.php t.php money.php; do \
         if [ -f "/var/www/html/$f" ]; then cp -f "/var/www/html/$f" "/var/www/html/admin/$f"; fi; \
       done \
    && find /var/www/html -maxdepth 1 -type f \( -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.gif' -o -iname '*.webp' \) -exec cp -n {} /var/www/html/database/upload/ \; \
    && cat > /var/www/html/database/conf.php <<'PHP'
<?php
$server = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'id18044649_food_website';
$port = (int)(getenv('DB_PORT') ?: 3306);
$conn = mysqli_connect($server, $username, $password, $database, $port);
if (!$conn) { die('Database connection failed.'); }
mysqli_set_charset($conn, 'utf8mb4');
?>
PHP

# Render supplies PORT. Apache must listen on it.
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 10000

CMD ["sh", "-c", "if [ -n \"$PORT\" ] && [ \"$PORT\" != \"10000\" ]; then sed -i \"s/Listen 10000/Listen $PORT/\" /etc/apache2/ports.conf; sed -i \"s/<VirtualHost \\*:10000>/<VirtualHost *:$PORT>/\" /etc/apache2/sites-available/000-default.conf; fi; apache2-foreground"]

#!/bin/sh
set -eu

PORT="${PORT:-10000}"
DB_NAME="${DB_NAME:-id18044649_food_website}"
DB_PASSWORD="${DB_PASSWORD:-}"
SQL_FILE="/var/www/html/id18044649_food_website.sql"

# Fast Food uses MariaDB inside this same Render container.
mkdir -p /run/mysqld
chown mysql:mysql /run/mysqld

if [ ! -d /var/lib/mysql/mysql ]; then
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/tmp/mysql-init.log 2>&1
fi

mysqld_safe --datadir=/var/lib/mysql --bind-address=127.0.0.1 --skip-name-resolve >/tmp/mysql.log 2>&1 &

i=0
until mariadb-admin ping --silent >/dev/null 2>&1; do
    i=$((i + 1))
    [ "$i" -lt 60 ] || { cat /tmp/mysql.log; exit 1; }
    sleep 1
done

# Create the application database first.
mariadb -uroot <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL

# Import the original complete database dump on a fresh container.
# This keeps the real project schema/data instead of a reduced hand-written schema.
if [ -f "$SQL_FILE" ]; then
    if ! mariadb -uroot "$DB_NAME" -e "SELECT 1 FROM banner LIMIT 1" >/dev/null 2>&1; then
        mariadb -uroot "$DB_NAME" < "$SQL_FILE"
    fi
fi

# PHP connects over TCP. Configure root for both local socket and TCP access.
mariadb -uroot <<SQL
ALTER USER 'root'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL

# Configure Apache for Render's assigned port.
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

exec apache2-foreground

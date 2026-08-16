#!/bin/sh
set -eu

PORT="${PORT:-10000}"
DB_NAME="${DB_NAME:-id18044649_food_website}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

# Start the bundled MariaDB when no external DB_HOST is supplied.
if [ -z "${DB_HOST:-}" ] || [ "${DB_HOST}" = "127.0.0.1" ] || [ "${DB_HOST}" = "localhost" ]; then
    mkdir -p /run/mysqld
    chown mysql:mysql /run/mysqld

    if [ ! -d /var/lib/mysql/mysql ]; then
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/tmp/mysql-init.log 2>&1
    fi

    mysqld_safe --datadir=/var/lib/mysql --bind-address=127.0.0.1 >/tmp/mysql.log 2>&1 &

    i=0
    until mariadb-admin ping --silent >/dev/null 2>&1; do
        i=$((i + 1))
        [ "$i" -lt 60 ] || { cat /tmp/mysql.log; exit 1; }
        sleep 1
    done

    # Create the database.
    mariadb -uroot <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL

    # The bundled MariaDB initially allows root through the local Unix socket,
    # but PHP connects over TCP (127.0.0.1). Ensure root can authenticate over
    # TCP as well, using the same DB_PASSWORD supplied to the application.
    mariadb -uroot <<SQL
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL

    # Create the tables required by the original application if they do not exist.
    mariadb -uroot "${DB_NAME}" <<'SQL'
CREATE TABLE IF NOT EXISTS banner (id int NOT NULL AUTO_INCREMENT, b_id varchar(50) NOT NULL, u_id varchar(50) NOT NULL, cat_id varchar(50) NOT NULL, scat_id varchar(50) NOT NULL, b_title varchar(100) NOT NULL, b_subtitle varchar(100) NOT NULL, b_desc varchar(200) NOT NULL, b_image varchar(100) NOT NULL, status varchar(50) NOT NULL DEFAULT 'show', PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS card (cr_id int NOT NULL AUTO_INCREMENT, inv_id varchar(50) NOT NULL, cat_id varchar(50) NOT NULL, scat_id varchar(50) NOT NULL, pro_id varchar(50) NOT NULL, u_id varchar(50) NOT NULL, qty decimal(10,0) NOT NULL, prize decimal(10,0) NOT NULL, tax int NOT NULL DEFAULT 3, date varchar(50) NOT NULL, status varchar(20) NOT NULL, number varchar(11), address varchar(100), PRIMARY KEY(cr_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS cash (id int NOT NULL AUTO_INCREMENT, `cash-in` decimal(10,0) NOT NULL, `cash-out` decimal(10,0) NOT NULL, invetment decimal(10,0) NOT NULL, profite decimal(10,0) NOT NULL, extra decimal(10,0) NOT NULL, date varchar(50) NOT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS catagory (id int NOT NULL AUTO_INCREMENT, cat_id varchar(50) NOT NULL, u_id varchar(50) NOT NULL, cat_name varchar(20) NOT NULL, status varchar(20) NOT NULL DEFAULT 'show', PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS colors (clr_id int NOT NULL AUTO_INCREMENT, hsl varchar(50) NOT NULL, clr varchar(50) NOT NULL, color_alt varchar(50) NOT NULL, color_lighter varchar(50) NOT NULL, clr_sts varchar(50) NOT NULL DEFAULT 'white', PRIMARY KEY(clr_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS feedback (f_id int NOT NULL AUTO_INCREMENT, user_id int NOT NULL, inv_id varchar(50) NOT NULL, msg varchar(150) NOT NULL, date datetime NOT NULL, PRIMARY KEY(f_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS product (id int NOT NULL AUTO_INCREMENT, p_id varchar(50) NOT NULL, cat_id varchar(50) NOT NULL, scat_id varchar(50) NOT NULL, u_id varchar(50) NOT NULL, p_title varchar(30) NOT NULL, p_subtitle varchar(30) NOT NULL, p_desc varchar(150) NOT NULL, p_prize decimal(10,0) NOT NULL, p_image varchar(50) NOT NULL, status varchar(20) NOT NULL DEFAULT 'show', action varchar(10) NOT NULL DEFAULT 'far', date datetime(6) NOT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS pro_stock (pp_id int NOT NULL AUTO_INCREMENT, ps_id varchar(50) NOT NULL, cat_id varchar(50) NOT NULL, scat_id varchar(50) NOT NULL, pro_id varchar(50) NOT NULL, u_id int NOT NULL, qty decimal(10,0) NOT NULL, prize decimal(10,0) NOT NULL, tax int NOT NULL DEFAULT 3, date varchar(50) NOT NULL, status varchar(20) NOT NULL, PRIMARY KEY(pp_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS register (u_id int NOT NULL AUTO_INCREMENT, unique_id int NOT NULL, Name varchar(30) NOT NULL, email varchar(30) NOT NULL, password varchar(50) NOT NULL, image varchar(60) NOT NULL, status varchar(60) NOT NULL, role_id int NOT NULL DEFAULT 2, address varchar(100) NOT NULL DEFAULT 'karachi', number varchar(14) NOT NULL DEFAULT 'N/A', PRIMARY KEY(u_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS role (role_id int NOT NULL AUTO_INCREMENT, role_name varchar(20) NOT NULL, PRIMARY KEY(role_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS sub_category (id int NOT NULL AUTO_INCREMENT, scat_id varchar(50) NOT NULL, cat_id varchar(50) NOT NULL, u_id varchar(50) NOT NULL, scat_name varchar(50) NOT NULL, status varchar(50) NOT NULL DEFAULT 'show', PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO role (role_id, role_name) SELECT 1,'admin' WHERE NOT EXISTS (SELECT 1 FROM role WHERE role_id=1);
INSERT INTO role (role_id, role_name) SELECT 2,'user' WHERE NOT EXISTS (SELECT 1 FROM role WHERE role_id=2);
INSERT INTO colors (hsl,clr,color_alt,color_lighter,clr_sts) SELECT '0','red','#f00','#faa','white' WHERE NOT EXISTS (SELECT 1 FROM colors);
SQL
fi

# Configure Apache for Render's assigned port.
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

exec apache2-foreground

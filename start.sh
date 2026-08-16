#!/bin/sh
set -eu

PORT="${PORT:-10000}"

# Render provides PORT at runtime. Make Apache listen on that port.
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:10000>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground

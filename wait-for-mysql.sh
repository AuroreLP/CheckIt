#!/bin/sh

HOST="${MYSQL_HOST:-mysql}"

echo "⏳ Attente de MySQL sur $HOST..."
until mysqladmin ping -h"$HOST" --silent; do
    sleep 2
done

echo "✅ MySQL est prêt, démarrage de php-fpm et nginx..."
php-fpm -D
exec nginx -g 'daemon off;'

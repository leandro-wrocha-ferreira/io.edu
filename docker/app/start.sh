#!/bin/sh

composer install --prefer-dist --optimize-autoloader

echo "Waiting for database connection..."
max_tries=30
count=0
until php -r "
	\$host = getenv('DB_HOSTNAME') ?: 'db';
	\$user = getenv('DB_USERNAME') ?: 'root';
	\$pass = getenv('DB_PASSWORD') ?: '';
	\$db   = getenv('DB_DATABASE') ?: 'io-edu';
	\$mysqli = @new mysqli(\$host, \$user, \$pass, \$db);
	if (!\$mysqli->connect_error) {
		\$mysqli->close();
		exit(0);
	}
	exit(1);
" > /dev/null 2>&1; do
	count=$((count + 1))
	if [ $count -ge $max_tries ]; then
		echo "Database connection timeout after 60 seconds."
		break
	fi
	echo "Database not ready yet... retrying in 2 seconds ($count/$max_tries)"
	sleep 2
done

echo "Database is ready! Running migrations..."
php index.php Migrations migrate

nginx

exec php-fpm

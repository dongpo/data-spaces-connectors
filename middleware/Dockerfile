FROM mirror.gcr.io/library/php:8.2-apache-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends libpng-dev libonig-dev libcurl4-openssl-dev libzip-dev && docker-php-ext-install pdo_mysql mysqli gd mbstring curl zip && a2enmod rewrite headers && rm -rf /var/lib/apt/lists/*
COPY source/ /var/www/html/
RUN printf '<Directory /var/www/html>\nAllowOverride All\nRequire all granted\n</Directory>\n<FilesMatch "\\.(key|pem|sql|xlsx)$">\nRequire all denied\n</FilesMatch>\n' > /etc/apache2/conf-available/ttc.conf && a2enconf ttc && find /var/www/html -type d -exec chmod 755 {} + && find /var/www/html -type f -exec chmod 644 {} + && mkdir -p /var/www/html/uploadfile && chown -R www-data:www-data /var/www/html/uploadfile
EXPOSE 80

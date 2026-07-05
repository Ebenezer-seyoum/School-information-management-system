FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

CMD PORT="${PORT:-80}"; \
    echo "Listen ${PORT}" > /etc/apache2/ports.conf; \
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf; \
    apache2-foreground

EXPOSE 80

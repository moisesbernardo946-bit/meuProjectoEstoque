FROM php:8.2-apache

# Instalar dependências do PHP e do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar projeto
WORKDIR /var/www/html
COPY . /var/www/html

# Apontar Apache para a pasta public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Rodar migrations e seeders automaticamente (somente no deploy inicial)
RUN php artisan migrate --force && php artisan db:seed --force

EXPOSE 80
CMD ["apache2-foreground"]

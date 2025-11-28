FROM php:8.2-apache

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar projeto
WORKDIR /var/www/html
COPY . /var/www/html

# Apontar DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]

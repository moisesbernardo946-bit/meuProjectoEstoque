# Imagem base PHP 8.2 + Apache
FROM php:8.2-apache

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd bcmath intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar todo o projeto
COPY . /var/www/html

# Configurar Apache para servir /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Criar pasta de QR codes com permissão
RUN mkdir -p /var/www/html/public/qrcodes \
    && chmod -R 775 /var/www/html/public/qrcodes \
    && chown -R www-data:www-data /var/www/html/public/qrcodes

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões corretas
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Expõe porta 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]

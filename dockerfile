# Usando PHP 8.2 com Apache
FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do Laravel para o container
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Definir permissões
RUN chmod -R 775 storage bootstrap/cache

# Expor a porta do Apache
EXPOSE 80

# Rodar o servidor Apache
CMD ["apache2-foreground"]

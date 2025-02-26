# Usando PHP 8.2
FROM php:8.2-cli

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

# Criar diretórios necessários e definir permissões
RUN mkdir -p storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Copiar o script de inicialização
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expor a porta do servidor embutido do PHP
EXPOSE 8000

# Definir o script de inicialização
ENTRYPOINT ["/entrypoint.sh"]
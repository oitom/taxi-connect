# Use a imagem oficial do PHP 8 com Apache
FROM php:8.1-alpine

# Instale as dependências necessárias
RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip

# Instale e configure o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instale o SDK da AWS usando o Composer
RUN composer require aws/aws-sdk-php

# Ative o módulo rewrite do Apache
RUN a2enmod rewrite

# Configure o diretório raiz do Apache
WORKDIR /var/www/html

# Copie os arquivos da aplicação para o contêiner
COPY . .

# Copia os arquivos do Composer
COPY composer.json /var/www/html/

# Instala as dependências do Composer
RUN composer install --dev
RUN chmod 777 /var/www/html/data

# Comando padrão para iniciar o Apache (entrypoint do Apache)
CMD ["apache2-foreground"]
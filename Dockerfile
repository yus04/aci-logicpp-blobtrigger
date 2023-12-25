FROM php:7.4

WORKDIR /app/

COPY index.php /app/

RUN apt-get update \
    && apt-get install -y unzip git \
    && rm -rf /var/lib/apt/lists/*

# PDOとPDO MySQL拡張機能をインストール
RUN docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/app/ --filename=composer \
    && ./composer require "microsoft/azure-storage-blob"

CMD ["php", "index.php"]

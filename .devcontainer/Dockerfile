FROM mcr.microsoft.com/devcontainers/php:8.3

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y \
        default-mysql-client \
        git \
        unzip \
        curl \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        bcmath \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
#!/bin/bash

# Instalar extensão MySQL
sudo apt-get update
sudo apt-get install -y php8.3-mysql

# Iniciar MySQL
sudo service mysql start

# Aguardar MySQL iniciar
sleep 5

# Criar banco central se não existir
sudo mysql -u root -e "CREATE DATABASE IF NOT EXISTS app_central;"

# Instalar dependências
composer install

# Configurar .env
cp .env.example .env
php artisan key:generate

# Instalar Node
npm install

# Rodar migrations
php artisan migrate:fresh --seed

# Criar tenant demo e popular
php artisan tenants:create demo
php artisan tenants:migrate --tenant=demo
php artisan tenants:seed --class=TenantProdutosSeeder --tenant=demo

echo "✅ Setup completo!"
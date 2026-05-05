#!/bin/bash

set -e

echo "🚀 Iniciando setup do Laravel + MySQL..."

echo "⏳ Aguardando MySQL iniciar..."
until mysqladmin ping -h mysql -u root -proot --silent; do
    sleep 2
done

echo "✅ MySQL disponível."

echo "🗄️ Garantindo banco central..."
mysql -h mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS app_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "📄 Configurando .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

set_env() {
    KEY=$1
    VALUE=$2

    if grep -q "^${KEY}=" .env; then
        sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" .env
    else
        echo "${KEY}=${VALUE}" >> .env
    fi
}

set_env "APP_ENV" "local"
set_env "DB_CONNECTION" "mysql"
set_env "DB_HOST" "mysql"
set_env "DB_PORT" "3306"
set_env "DB_DATABASE" "app_central"
set_env "DB_USERNAME" "root"
set_env "DB_PASSWORD" "root"

echo "📦 Instalando dependências PHP..."
composer install

echo "🔑 Gerando APP_KEY..."
php artisan key:generate --force

echo "📦 Instalando dependências Node..."
npm install

echo "🧹 Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Ambiente pronto!"
echo ""
echo "Agora rode manualmente, com cuidado:"
echo "php artisan migrate"
echo "php artisan serve --host=0.0.0.0 --port=8000"
comandos para usar no codespace
# 1. Entrar no MySQL
sudo mysql -u root

# 2. Dentro do MySQL, ver os bancos:
SHOW DATABASES;

# 3. Usar o banco central:
USE app_central;

# 4. Ver as tabelas:
SHOW TABLES;

# 5. Ver os usuários (super admin):
SELECT * FROM users;

# 6. Ver os planos:
SELECT * FROM plans;

# 7. Ver os tenants:
SELECT * FROM tenants;

# Ver usuários
sudo mysql -u root -e "USE app_central; SELECT * FROM users;"

# Ver super admin especificamente
sudo mysql -u root -e "USE app_central; SELECT * FROM users WHERE is_super_admin = 1;"

# Ver planos
sudo mysql -u root -e "USE app_central; SELECT * FROM plans;"



✅ Comandos corretos para sua versão:
bash
# Para rodar migrations em TODOS os tenants
php artisan tenants:migrate

# Para rodar migrations em UM tenant específico
php artisan tenants:migrate --tenant=demo

# Para rodar migrations FRESCAS (recriar tudo) em todos os tenants
php artisan tenants:migrate-fresh

# Para rodar migrations FRESCAS em um tenant específico
php artisan tenants:migrate-fresh --tenant=demo

# Para rodar ROLLBACK em todos os tenants
php artisan tenants:rollback

# Para rodar ROLLBACK em um tenant específico
php artisan tenants:rollback --tenant=demo


🚀 Execute agora:
bash
# 1. Primeiro, rode as migrations no seu tenant demo
php artisan tenants:migrate --tenant=demo

# 2. Ou se quiser rodar em todos os tenants (quando tiver mais)
php artisan tenants:migrate

# 3. Verifique se as tabelas foram criadas
mysql -u root -p
USE tenant_demo;
SHOW TABLES;





# Migrations
php artisan tenants:migrate          # Roda migrations em todos os tenants
php artisan tenants:migrate --tenant=demo  # Em um tenant específico
php artisan tenants:migrate-fresh     # Recria tudo em todos
php artisan tenants:rollback          # Rollback em todos

# Seeders
php artisan tenants:seed              # Roda seeders em todos
php artisan tenants:seed --class=DatabaseSeeder --tenant=demo

# Criar novo tenant via CLI
php artisan tenants:create demo2

# Listar tenants
php artisan tenants:list


# Iniciar o MySQL
sudo service mysql start

# Parar o MySQL
sudo service mysql stop

# Reiniciar o MySQL
sudo service mysql restart

# Verificar status
sudo service mysql status

# Verificar se está rodando
sudo mysqladmin ping

# Entrar como root (sem senha)
sudo mysql -u root

# Ou com senha (se tiver definido)
mysql -u root -p
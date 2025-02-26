#!/bin/sh

# Rodar as migrações
php artisan migrate --force

# Criar usuário administrador
php artisan tinker --execute="use App\Models\User; User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'is_admin' => true]);"

# Iniciar o servidor embutido do PHP
exec php -S 0.0.0.0:8000 -t public
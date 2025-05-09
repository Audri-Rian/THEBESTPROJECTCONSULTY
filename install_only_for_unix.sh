#!/bin/bash

echo "
8888888      .d8888b.      888           .d8888b.      888     888    
  888       d88P  Y88b     888          d88P  Y88b     888     888    
  888       888    888     888          Y88b.          888     888    
  888       888            888           Y888b.        888     888    
  888       888            888              Y88b.      888     888    
  888       888    888     888                888      888     888    
  888   d8b Y88b  d88P d8b 888      d8b Y88b  d88P d8b Y88b. .d88P d8b
8888888 Y8P  "Y8888P"  Y8P 88888888 Y8P  "Y8888P"  Y8P  "Y88888P"  d8b
"

echo "Seja bem-vindo ao instalador Laravel Automático, desenvolvido por Softboy Corporation."

install_dependencies() {
    echo "Instalando dependências PHP..."
    composer install > /dev/null 2>&1 || { echo "Falha ao instalar dependências PHP."; exit 1; }
    echo "Dependências PHP instaladas."

    echo "Instalando dependências Node.js..."
    npm install > /dev/null 2>&1 || { echo "Falha ao instalar dependências Node.js."; exit 1; }
    echo "Dependências Node.js instaladas."
}

setup_env_file() {
    if [ -f .env ]; then
        echo ".env já existe. Pulando cópia."
    else
        cp .env.example .env
        echo "Copiado .env.example para .env"
    fi
}

update_db_connection() {
    if [ -f .env ]; then
        sed -i 's/^DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
        echo "Atualizado DB_CONNECTION para mysql no arquivo .env."
    else
        echo ".env não encontrado. Pulando atualização de DB_CONNECTION."
    fi
}

configure_database() {
    if [ -f .env ]; then
        sed -i 's/^# DB_HOST=/DB_HOST=/' .env
        sed -i 's/^# DB_PORT=/DB_PORT=/' .env
        sed -i 's/^# DB_DATABASE=/DB_DATABASE=/' .env
        sed -i 's/^# DB_USERNAME=/DB_USERNAME=/' .env
        sed -i 's/^# DB_PASSWORD=/DB_PASSWORD=/' .env
        echo "Removidos comentários das configurações do banco de dados no arquivo .env."
    fi

    DB_HOST_DEFAULT="127.0.0.1"
    DB_PORT_DEFAULT="3306"
    DB_DATABASE_DEFAULT="laravel"
    DB_USERNAME_DEFAULT="root"

    read -p "Digite o host do banco de dados (padrão: $DB_HOST_DEFAULT): " DB_HOST
    DB_HOST=${DB_HOST:-$DB_HOST_DEFAULT}

    read -p "Digite a porta do banco de dados (padrão: $DB_PORT_DEFAULT): " DB_PORT
    DB_PORT=${DB_PORT:-$DB_PORT_DEFAULT}

    read -p "Digite o nome do banco de dados (padrão: $DB_DATABASE_DEFAULT): " DB_DATABASE
    DB_DATABASE=${DB_DATABASE:-$DB_DATABASE_DEFAULT}

    read -p "Digite o usuário do banco de dados (padrão: $DB_USERNAME_DEFAULT): " DB_USERNAME
    DB_USERNAME=${DB_USERNAME:-$DB_USERNAME_DEFAULT}

    read -sp "Digite a senha do banco de dados: " DB_PASSWORD
    echo

    if [ -f .env ]; then
        sed -i "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" .env
        sed -i "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" .env
        sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
        sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
        sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
        echo "Configuração do banco de dados atualizada no arquivo .env."
    else
        echo ".env não encontrado. Pulando atualização de configuração do banco."
    fi

    echo "Limpeza do cache de configuração do Laravel..."
    php artisan config:clear > /dev/null 2>&1 || { echo "Falha ao limpar o cache de configuração do Laravel."; exit 1; }
}

run_artisan_commands() {
    echo "Gerando chave da aplicação..."
    php artisan key:generate > /dev/null 2>&1 || { echo "Falha ao gerar chave da aplicação. Verifique sua instalação do PHP."; exit 1; }

    echo "Rodando migrações..."
    php artisan migrate > /dev/null 2>&1 || { echo "Falha ao rodar as migrações. Verifique a conexão com o banco."; exit 1; }

    echo "Semeando o banco de dados..."
    php artisan db:seed > /dev/null 2>&1 || { echo "Falha ao semear o banco de dados. Verifique a conexão com o banco."; exit 1; }
}

echo "Iniciando o processo de configuração..."

install_dependencies
setup_env_file
update_db_connection
configure_database
run_artisan_commands

echo "Configuração concluída com sucesso!"
echo "Você pode iniciar o servidor web com o comando: composer run dev"
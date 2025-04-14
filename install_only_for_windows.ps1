Write-Host "
8888888      .d8888b.      888           .d8888b.      888     888    
  888       d88P  Y88b     888          d88P  Y88b     888     888    
  888       888    888     888          Y88b.          888     888    
  888       888            888           Y888b.        888     888    
  888       888            888              Y88b.      888     888    
  888       888    888     888                888      888     888    
  888   d8b Y88b  d88P d8b 888      d8b Y88b  d88P d8b Y88b. .d88P d8b
8888888 Y8P  "Y8888P"  Y8P 88888888 Y8P  "Y8888P"  Y8P  "Y88888P"  d8b
"

Write-Host "Seja bem-vindo ao instalador Laravel Automático, desenvolvido por Softboy Corporation."

function install_dependencies {
    Write-Host "Instalando dependências PHP..."
    try {
        composer install | Out-Null
        Write-Host "Dependências PHP instaladas."
    } catch {
        Write-Host "Falha ao instalar dependências PHP."
        exit
    }

    Write-Host "Instalando dependências Node.js..."
    try {
        npm install | Out-Null
        Write-Host "Dependências Node.js instaladas."
    } catch {
        Write-Host "Falha ao instalar dependências Node.js."
        exit
    }
}

function setup_env_file {
    if (Test-Path .env) {
        Write-Host ".env já existe. Pulando cópia."
    } else {
        Copy-Item .env.example .env
        Write-Host "Copiado .env.example para .env"
    }
}

function update_db_connection {
    if (Test-Path .env) {
        (Get-Content .env) | ForEach-Object { $_ -replace '^DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' } | Set-Content .env
        Write-Host "Atualizado DB_CONNECTION para mysql no arquivo .env."
    } else {
        Write-Host ".env não encontrado. Pulando atualização de DB_CONNECTION."
    }
}

function configure_database {
    if (Test-Path .env) {
        (Get-Content .env) | ForEach-Object { 
            $_ -replace '^# DB_HOST=', 'DB_HOST=' 
            $_ -replace '^# DB_PORT=', 'DB_PORT=' 
            $_ -replace '^# DB_DATABASE=', 'DB_DATABASE=' 
            $_ -replace '^# DB_USERNAME=', 'DB_USERNAME=' 
            $_ -replace '^# DB_PASSWORD=', 'DB_PASSWORD=' 
        } | Set-Content .env
        Write-Host "Removidos comentários das configurações do banco de dados no arquivo .env."
    }

    $DB_HOST_DEFAULT = "127.0.0.1"
    $DB_PORT_DEFAULT = "3306"
    $DB_DATABASE_DEFAULT = "laravel"
    $DB_USERNAME_DEFAULT = "root"

    $DB_HOST = Read-Host "Digite o host do banco de dados (padrão: $DB_HOST_DEFAULT)"
    $DB_HOST = if ($DB_HOST) { $DB_HOST } else { $DB_HOST_DEFAULT }

    $DB_PORT = Read-Host "Digite a porta do banco de dados (padrão: $DB_PORT_DEFAULT)"
    $DB_PORT = if ($DB_PORT) { $DB_PORT } else { $DB_PORT_DEFAULT }

    $DB_DATABASE = Read-Host "Digite o nome do banco de dados (padrão: $DB_DATABASE_DEFAULT)"
    $DB_DATABASE = if ($DB_DATABASE) { $DB_DATABASE } else { $DB_DATABASE_DEFAULT }

    $DB_USERNAME = Read-Host "Digite o usuário do banco de dados (padrão: $DB_USERNAME_DEFAULT)"
    $DB_USERNAME = if ($DB_USERNAME) { $DB_USERNAME } else { $DB_USERNAME_DEFAULT }

    $DB_PASSWORD = Read-Host "Digite a senha do banco de dados"

    if (Test-Path .env) {
        (Get-Content .env) | ForEach-Object { 
            $_ -replace "^DB_HOST=.*", "DB_HOST=$DB_HOST"
            $_ -replace "^DB_PORT=.*", "DB_PORT=$DB_PORT"
            $_ -replace "^DB_DATABASE=.*", "DB_DATABASE=$DB_DATABASE"
            $_ -replace "^DB_USERNAME=.*", "DB_USERNAME=$DB_USERNAME"
            $_ -replace "^DB_PASSWORD=.*", "DB_PASSWORD=$DB_PASSWORD"
        } | Set-Content .env
        Write-Host "Configuração do banco de dados atualizada no arquivo .env."
    } else {
        Write-Host ".env não encontrado. Pulando atualização de configuração do banco."
    }

    Write-Host "Limpeza do cache de configuração do Laravel..."
    try {
        php artisan config:clear | Out-Null
    } catch {
        Write-Host "Falha ao limpar o cache de configuração do Laravel."
        exit
    }
}

function run_artisan_commands {
    Write-Host "Gerando chave da aplicação..."
    try {
        php artisan key:generate | Out-Null
    } catch {
        Write-Host "Falha ao gerar chave da aplicação. Verifique sua instalação do PHP."
        exit
    }

    Write-Host "Rodando migrações..."
    try {
        php artisan migrate | Out-Null
    } catch {
        Write-Host "Falha ao rodar as migrações. Verifique a conexão com o banco."
        exit
    }

    Write-Host "Semeando o banco de dados..."
    try {
        php artisan db:seed | Out-Null
    } catch {
        Write-Host "Falha ao semear o banco de dados. Verifique a conexão com o banco."
        exit
    }
}

Write-Host "Iniciando o processo de configuração..."

install_dependencies
setup_env_file
update_db_connection
configure_database
run_artisan_commands

Write-Host "Configuração concluída com sucesso!"
Write-Host "Você pode iniciar o servidor web com o comando: composer run dev"

# 📦 Sistema de Gestão Emprensarial (ERP)

Este projeto surgiu como parte de uma iniciativa de extensão da minha faculdade, na qual fui designado para prestar consultoria a uma empresa parceira. Durante as reuniões iniciais, identificamos que a organização enfrentava dificuldades crescentes na gestão integrada de suas finanças e do seu estoque, resultando em retrabalho, perda de informações e atraso na tomada de decisões. Como coordenador desse projeto de extensão, decidi desenvolver um Sistema de Gestão Empresarial (ERP) sob medida para atender às necessidades específicas dessa empresa.

O objetivo principal é criar uma plataforma única, construída em PHP com o framework Laravel, que centralize e automatize processos críticos de controle financeiro (como contas a pagar, contas a receber, fluxo de caixa e geração de relatórios) e de gestão de estoque (como registro de entradas e saídas, controle de níveis mínimos de produto, inventário e relatórios de movimentação). Com isso, pretendemos oferecer maior visibilidade sobre o desempenho operacional, facilitar a conciliação de dados e reduzir erros manuais.

Ao longo deste documento, iremos detalhar cada módulo, apresentar exemplos de código comentado e propor sugestões de melhorias para que qualquer iniciante em desenvolvimento consiga compreender, utilizar e, se necessário, customizar o sistema.

## Table of Contents

* [ ✨ Sobre o Projeto](#sobreoprojeto)
* [ 🚤 Instalação](#instalacao)
* [ 🔧 Funcionalidades](#funcionalidades)
* [ 📜 Documentações Presentes](#documentation)
* [ 🎥 Software](#-demonstração-em-vídeo)
* [ ✍️ Contribuições](#contribuitions)
* [ 📢 Contato](#contact)
## ✨ Sobre o Projeto

O Sistema de Gestão Empresarial (ERP) é uma aplicação em PHP/Laravel desenvolvida para oferecer uma solução integrada de controle financeiro e de estoque para pequenas e médias empresas. Projetado como parte de um projeto de extensão universitária que presto consultoria a uma empresa parceira, o ERP foi idealizado para centralizar processos, reduzir falhas manuais e fornecer informações em tempo real para a tomada de decisões estratégicas.

Modularidade: diferentes módulos — Financeiro e Estoque — atuam de forma independente, permitindo evolução e manutenção isolada sem impactar todo o sistema.
Testabilidade: a clara separação entre Controllers, Services e Repositories facilita a criação de testes unitários e de integração para cada camada de negócio.
Manutenibilidade: a adoção de padrões de projeto (Repository, Service, Factory) e da arquitetura MVC do Laravel garante um código limpo, organizado e de fácil entendimento.
Segurança: validações via Form Requests, sanitização de entradas e uso do Eloquent ORM protegem contra XSS e SQL Injection. Políticas (Policies) e Gates do Laravel controlam permissões de acesso em nível granular.
Escalabilidade: a estrutura modular permite a adição futura de novos módulos (vendas, compras, CRM), sem necessidade de refatorações profundas no núcleo do sistema.

Além disso, o ERP utiliza injeção de dependência nativa do Laravel (Container IoC) para gerenciar serviços e repositórios, adotando convenções de conveniência do framework. Integrado ao Blade para views dinâmicas, Eloquent para acesso a dados e middlewares para autenticação e autorização, o sistema simplifica a construção de rotas, middlewares personalizados e comandos Artisan. Dessa forma, otimizamos o desenvolvimento, garantindo reuso de componentes e alinhamento com as boas práticas do ecossistema Laravel.

## 🚤 Instalação

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- PHP 8.1 ou superior
- Composer
- Node.js (versão 16 ou superior)
- npm ou yarn
- MySQL 8.0 ou superior
- Git

## 🚀 Passo a Passo da Instalação

### 1. Clone o Repositório

```bash
git clone [URL_DO_REPOSITÓRIO]
cd Consultory-Project
```

### 2. Configuração do Ambiente

#### 2.1 Configuração do PHP/Laravel
```bash
# Instale as dependências do PHP
composer install

# Copie o arquivo de ambiente
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

#### 2.2 Configure o arquivo .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=consultory_db
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

#### 2.3 Instalação das Dependências Frontend
```bash
# Instale as dependências do Node.js
npm install
# ou se estiver usando yarn
yarn install
```

### 3. Configuração do Banco de Dados

```bash
# Crie o banco de dados
mysql -u root -p
CREATE DATABASE consultory_db;
exit;

# Execute as migrações
php artisan migrate

# (Opcional) Execute os seeders para dados de exemplo
php artisan db:seed
```

### 4. Compilação dos Assets

```bash
# Compile os assets para desenvolvimento
npm run dev
# ou para produção
npm run build
```

### 5. Configuração do Storage

```bash
# Crie o link simbólico para o storage
php artisan storage:link
```

### 6. Configurações de Permissões (apenas para Linux/Unix)

```bash
# Configure as permissões das pastas
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🌐 Iniciando o Projeto

### Para Ambiente de Desenvolvimento

1. Inicie o servidor Laravel:
```bash
php artisan serve
```

2. Em outro terminal, inicie o Vite para compilação dos assets:
```bash
npm run dev
```

3. Acesse o projeto em: `http://localhost:8000`

### Para Ambiente de Produção

1. Configure seu servidor web (Apache/Nginx)
2. Configure os domínios
3. Configure o SSL (recomendado)
4. Execute:
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Configurações Adicionais

### Configuração do Email (opcional)
No arquivo .env, configure as credenciais do seu servidor de email:
```env
MAIL_MAILER=smtp
MAIL_HOST=seu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=seu_email
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email_remetente
MAIL_FROM_NAME="${APP_NAME}"
```

### Configuração do WhatsApp (para o CTA da Landing Page)
No arquivo .env, adicione:
```env
WHATSAPP_NUMBER=seu_numero
WHATSAPP_MESSAGE=mensagem_padrao
```

## 🛠️ Solução de Problemas Comuns

1. **Erro de permissão no storage:**
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

2. **Erro de cache:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

3. **Erro de dependências:**
```bash
composer dump-autoload
npm clean-install
```

## ⚠️ Notas Importantes

- Mantenha sempre backups do banco de dados
- Em produção, configure adequadamente o arquivo .env
- Utilize sempre HTTPS em produção
- Configure corretamente as permissões de usuário
- Mantenha todas as dependências atualizadas

## 📞 Suporte

Em caso de dúvidas ou problemas:
1. Consulte a documentação oficial do Laravel
2. Verifique os logs em `storage/logs`
3. Entre em contato com a equipe de desenvolvimento

## 🔄 Atualizações

Para atualizar o projeto:
```bash
git pull
composer install
npm install
php artisan migrate
npm run build
``` 

## 🔧 Funcionalidades



## 📜 Documentações Presentes
- Análise de Requisitos
- Documentação Principal

## 🎥 Software

## ✍️ Contribuições
| Responsável | Função                         | Principais Responsabilidades Técnicas                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| :---------- | :----------------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Audri-Rian  | Gerente de Projeto / Tech Lead | - Definição da arquitetura geral em Laravel (MVC, Repositories, Services, Factories).<br>- Criação e manutenção de guidelines de codificação (PSR, SOLID, Clean Code).<br>- Planejamento de sprints e milestones.<br>- Revisão de pull requests para garantir qualidade, segurança e padronização.<br>- Desenvolvedor back-end.<br> - Desenvolvedor front-end.<br>- Documentação de fluxos críticos (autorizações, tratamentos de erro e integrações externas). |
| kami-note    | Líder de Back-End | - Modelagem do banco de dados MySQL (tabelas, relacionamentos, índices e migrations Laravel).<br>- Definição de contratos de API RESTful (rotas, controllers e recursos JSON).<br>- Implementação de padrões Repository e Service para abstração de lógica de negócio e acesso a dados.<br>- Configuração de autenticação e autorização (Sanctum / Passport).<br>- Estruturação de testes unitários e de integração usando PHPUnit e Mockery.<br>- Otimização de queries Eloquent e índices para performance em produção.                             |
| PedroFelix77 | Back-End          | - Implementação de endpoints RESTful (Controllers e Resources em Laravel).<br>- Criação de Form Requests para validação e sanitização de payloads.<br>- Desenvolvimento de jobs e eventos (Mailables, Notifications, Queues) para tarefas assíncronas (e.g., envio de notificações por e-mail).<br>- Integração com serviços externos (e.g., APIs bancárias para conciliação).<br>- Configuração de middlewares personalizados (logs, verificação de permissões e throttling).<br>- Apoio na criação de Seeder e Factories para testes automatizados.
| maykonzx7   | Front-End                | - Construção de componentes Vue.js (ou Livewire) para formulários de cadastro e listagens dinâmicas.<br>- Implementação de layouts responsivos com Tailwind CSS (grid, breakpoints e utilitários personalizados).<br>- Consumo de APIs REST via Axios, gerenciamento de estado local e tratamento de erros de requisição.<br>- Criação de páginas Blade integradas a componentes SPA para dashboard financeiro.<br>- Otimização de performance (lazy loading de scripts e otimização de imagens).                                |
| KelvKVS     | Front-End                | - Desenvolvimento de interfaces de usuário focadas em UX (formulários, tabelas e modais) usando Blade e componentes reutilizáveis.<br>- Implementação de validação de formulário no lado do cliente (JavaScript) antes do envio ao Back-End.<br>- Configuração de Webpack Mix para compilação de assets (SCSS, JS) com versionamento de arquivos.<br>- Ajustes de acessibilidade (WCAG): atributos ARIA, foco em teclado e contraste de cores.<br>- Testes manuais de compatibilidade entre navegadores (Chrome, Firefox, Edge). |
| Vini-Mago   | Front-End (Landing Page) | - Criação de Landing Page institucional usando Blade e layouts personalizados.<br>- Design responsivo com Tailwind CSS e adaptações para dispositivos móveis.<br>- Integração de formulários de contato com rotas do Laravel para envio de e-mail (Mailables).<br>- Uso de componentes animados (Alpine.js ou TinySlider.js) para seções interativas (carrossel de casos de uso, depoimentos).<br>- Otimização de SEO básico (meta tags dinâmicas, sitemap.xml e robots.txt).                                                    |


## 📢 Contato
- audririan1@gmail.com
- (75) 9 9294-4283
- www.linkedin.com/in/audri-rian-720068215

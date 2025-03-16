# 💎 Landing Page + Sistema Financeiro e Estoque

Este repositório reúne três projetos integrados com foco em captação de leads, controle financeiro e controle de estoque — tudo isso embalado com boas práticas de UX/UI, segurança e organização de dados.

---

## 📌 Projetos Envolvidos

### A. **Landing Page - Marketing, Captação de Leads e Apresentação Visual**

**Objetivo:**  
Página institucional para apresentação da marca e captação de leads.

**Produto/Serviço:**  
Joias e perfumes — foco visual e sensorial.

**Público-alvo:**  
Homens, mulheres, românticos, vaidosos… qualquer um impactado pela estética.

**📢 Principal CTA:**  
Botão **"Fale Conosco"** com redirecionamento para o WhatsApp.

#### ✅ Funcionalidades
- Formulário de contato (nome, e-mail, telefone, mensagem)
- Depoimentos e avaliações de clientes
- Seção de FAQ
- Design responsivo (mobile-first)
- Alta performance e carregamento rápido
- SEO previsto para fase futura

---

### B. **Sistema de Controle Financeiro - Organização, Visibilidade e Tomada de Decisão**

**Nome:** `FinanciController`

**Objetivo:**  
Organizar finanças, controlar fluxo de caixa e gerar relatórios.

**Usuários-alvo:**  
Empresas, setores administrativos e usuários pessoais.

#### ✅ Funcionalidades
- Cadastro de receitas e despesas (fixas, variáveis e operacionais)
- Registro de transações com data, valor e descrição
- Relatórios: fluxo de caixa, gráfico por categoria, mensal
- Inclusão opcional de anexos (notas fiscais, comprovantes)
- Dashboard financeiro com visão geral

#### ⚙ Requisitos Não Funcionais
- Responsividade (para o gestor que vive no celular)
- Criptografia e autenticação segura
- Alta performance com Laravel

---

### C. **Sistema de Controle de Estoque - Eficiência e Logística**

**Objetivo:**  
Gerenciar produtos, movimentações e estoque de forma clara e prática.

**Público-alvo:**  
Comércios em geral — de papelarias a pet shops.

#### ✅ Funcionalidades
- Cadastro de produtos (código, nome, quantidade, valor, fornecedor, fabricante)
- Registro de entradas/saídas
- Alertas de estoque baixo
- Cadastro de fornecedores
- Relatórios e histórico de movimentações
- Controle de usuários e permissões

#### ⚙ Requisitos Não Funcionais
- Design responsivo
- Segurança padrão Laravel (autenticação e criptografia)

---

## 👥 Atores do Sistema

- **Administrador:** Gerencia usuários, permissões e configurações.
- **Financeiro:** Lançamentos, controle de contas e relatórios.
- **Estoquista:** Gerencia produtos, movimentações e alertas.
- **Vendedor (opcional):** Realiza vendas que afetam automaticamente estoque e financeiro.

---

## 🔄 Lógica de Negócio

- **Venda:**  
  - Dá baixa no estoque  
  - Registra receita  
  - Atualiza saldo e fluxo de caixa

- **Compra de insumos/produtos:**  
  - Entrada no estoque  
  - Registra despesa  
  - Atualiza saldo e fluxo de caixa

---

## 💻 Tecnologias Utilizadas

- **Back-end:** PHP (Laravel)
- **Front-end:** HTML, CSS, Tailwind
- **Banco de Dados:** MySQL

---

## 📊 Relatórios e Indicadores

### Financeiros
- Despesas mensais
- Lucro bruto e líquido
- Ponto de equilíbrio
- Ticket médio
- ROI (Retorno sobre Investimento)

### Estoque
- Giro de estoque
- Produtos parados
- Cobertura (em dias)
- Curva ABC
- Ponto de pedido

---

## 🔐 Segurança & Permissões

- Autenticação por usuário
- Controle de permissões (estoque ≠ financeiro)

---

## ✨ Funcionalidades Extras

- Exportação de relatórios (PDF/Excel)
- Filtros por período/categoria
- Histórico completo de movimentações

---

## 📐 Fórmulas de Gestão Comercial

- **Margem de Lucro (%):** `(Lucro / Receita Total) × 100`
- **Ponto de Equilíbrio:** `Custos Fixos / (1 - (Custos Variáveis / Receita))`
- **Lucro Bruto:** `Receita Total - CMV`
- **Lucro Líquido:** `Lucro Bruto - Despesas Operacionais`
- **ROI:** `(Lucro Líquido / Investimento Total) × 100`
- **Ticket Médio:** `Receita Total / Número de Vendas`
- **Custo Total:** `Custos Fixos + Custos Variáveis`

---

## 📦 Fórmulas para Controle de Estoque

- **Giro de Estoque:** `CMV / Estoque Médio`
- **Cobertura (dias):** `(Estoque Atual × Período) / CMV`
- **CMV:** `Estoque Inicial + Compras - Estoque Final`
- **Curva ABC:**  
  - A: 20% dos produtos = 80% da receita  
  - B: 30% = 15%  
  - C: 50% = 5%
- **Ponto de Pedido:** `(Consumo Médio Diário × Tempo de Reposição) + Estoque de Segurança`

---

## 📅 Planejamento e Entrega

- **Prazo estimado:** 3 meses para todos os módulos
- **Gerenciamento:** Trello (sim, amamos um kanban)
- **Deploy:** Inicialmente local — com base para escalar
- **Controle de versão:** GitHub (nada de `final_final_v3_boaAgoraEssaMesmo.zip`)

---



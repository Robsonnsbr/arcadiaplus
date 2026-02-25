# Superstore - Documentação Técnica Completa

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Desenvolvido por:** Arcádia Technology

---

## Sumário

1. [Visão Geral](#visão-geral)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Módulos do Sistema](#módulos-do-sistema)
4. [Modelo de Dados](#modelo-de-dados)
5. [APIs e Endpoints](#apis-e-endpoints)
6. [Integrações Externas](#integrações-externas)
7. [Segurança e Autenticação](#segurança-e-autenticação)
8. [Guia de Implantação](#guia-de-implantação)

---

## Visão Geral

O **Superstore** é um Sistema Operacional Empresarial (Business Operating System) alimentado por Inteligência Artificial, projetado para revolucionar operações empresariais. O sistema integra cinco pilares fundamentais:

### Os 5 Pilares

1. **Knowledge Graph** - Grafo de conhecimento para dados empresariais interconectados
2. **Central Intelligence (Scientist)** - Módulo de IA para geração automática de soluções
3. **Manus (Agente Autônomo)** - Execução de tarefas e automação
4. **Centro de Comunicação Unificado** - Interação com clientes via múltiplas plataformas
5. **IDE Completa** - Ambiente de desenvolvimento multi-modal

### Segmentação de Produtos

| Produto | Público-Alvo | Stack Tecnológica |
|---------|-------------|-------------------|
| **Arcádia Plus** | Pequenas empresas | Node.js + Python + PostgreSQL |
| **Arcádia Next** | Médias/Grandes empresas | Frappe Framework + PostgreSQL |

Ambos compartilham o **Arcádia Fisco** como motor fiscal centralizado.

---

## Arquitetura do Sistema

### Arquitetura em 4 Camadas

```
┌─────────────────────────────────────────────────────────────────┐
│                    CAMADA DE APRESENTAÇÃO                        │
│   React 18 + TypeScript + Tailwind CSS + shadcn/ui              │
│   Interface estilo navegador com abas e omnibox                  │
│   Porta: 5000                                                    │
└─────────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   CAMADA DE ORQUESTRAÇÃO                         │
│   Express.js + Socket.IO + Manus Agent                          │
│   API REST + WebSocket em tempo real                            │
│   Porta: 5000                                                    │
└─────────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   CAMADA DE INTELIGÊNCIA                         │
│   FastAPI (Python) + OpenAI API                                 │
│   Scientist, Embeddings, RPA, Workflows                         │
│   Porta: 8001 (IA) / 8002 (Fisco)                               │
└─────────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CAMADA DE DADOS                             │
│   PostgreSQL + Knowledge Graph + ChromaDB                       │
│   Drizzle ORM + Session Store                                   │
└─────────────────────────────────────────────────────────────────┘
```

### Estrutura de Diretórios

```
arcadia-suite/
├── client/                    # Frontend React
│   └── src/
│       ├── components/        # Componentes reutilizáveis
│       ├── hooks/             # React hooks customizados
│       ├── lib/               # Utilitários e configurações
│       └── pages/             # Páginas da aplicação
├── server/                    # Backend Node.js
│   ├── admin/                 # Rotas administrativas
│   ├── api-central/           # Central de APIs
│   ├── automations/           # Motor de automações
│   ├── bi/                    # Business Intelligence
│   ├── chat/                  # Chat interno
│   ├── compass/               # Process Compass (clientes/projetos)
│   ├── crm/                   # Gestão de relacionamento
│   ├── email/                 # Serviço de e-mail
│   ├── erp/                   # Integração ERP
│   ├── fisco/                 # Motor fiscal (NF-e)
│   ├── ide/                   # IDE integrada
│   ├── learning/              # Sistema de aprendizado
│   ├── login-bridge/          # Bridge de autenticação
│   ├── manus/                 # Agente autônomo
│   ├── production/            # Gestão de produção
│   ├── productivity/          # Hub de produtividade
│   ├── proxy/                 # Proxy reverso
│   ├── python/                # Serviços Python (FastAPI)
│   ├── support/               # Central de suporte
│   ├── valuation/             # Precificação e valuation
│   └── whatsapp/              # Integração WhatsApp
└── shared/                    # Código compartilhado
    └── schema.ts              # Schemas do banco de dados
```

---

## Módulos do Sistema

### 1. Cockpit (Dashboard Principal)
**Arquivo:** `client/src/pages/Cockpit.tsx`

Painel central com visão geral do sistema:
- Widgets configuráveis
- Métricas em tempo real
- Atividades recentes
- Atalhos para módulos

### 2. Process Compass
**Arquivo:** `client/src/pages/ProcessCompass.tsx`  
**API:** `/api/compass/*`

Gestão completa de processos empresariais:
- **Clientes:** Cadastro, histórico, segmentação
- **Projetos:** Cronograma, tarefas, milestones
- **Contratos:** Gestão de contratos e renovações
- **Timesheet:** Controle de horas trabalhadas

### 3. Comunicação Unificada
**Arquivo:** `client/src/pages/Comunicacao.tsx`  
**API:** `/api/whatsapp/*`

Centro de comunicação multi-canal:
- **WhatsApp Business:** Atendimento via Baileys
- **Chat Interno:** Comunicação da equipe
- **E-mail:** Integração IMAP/SMTP
- **Tickets:** Sistema de filas de atendimento

### 4. CRM (Customer Relationship Management)
**Arquivo:** `client/src/pages/Crm.tsx`  
**API:** `/api/crm/*`

Gestão de relacionamento com clientes:
- Pipeline de vendas
- Funil de conversão
- Gestão de oportunidades
- Comissionamento automático
- Integração com Google Calendar

### 5. Business Intelligence (Arcádia Insights)
**Arquivo:** `client/src/pages/BiWorkspace.tsx`  
**API:** `/api/bi/*`

Análise e visualização de dados:
- Upload de arquivos (CSV, Excel)
- Gráficos interativos (Recharts)
- Dashboards personalizáveis
- Conexão com múltiplas fontes

### 6. Scientist (Central de Inteligência)
**Arquivo:** `client/src/pages/Scientist.tsx`  
**API:** `/api/scientist/*`

Módulo de auto-programação com IA:
- Análise de dados automatizada
- Geração de código (Python/SQL)
- Execução em sandbox
- Armazenamento de soluções reutilizáveis

### 7. Manus (Agente Autônomo)
**Arquivo:** `client/src/pages/Agent.tsx`  
**API:** `/api/manus/*`

Executor de tarefas autônomo:
- Loop pensamento-ação-observação
- Ferramentas disponíveis:
  - Busca web
  - Consulta ao Knowledge Graph
  - Consulta ERP
  - Cálculos
  - Envio de mensagens
  - Geração de relatórios
  - Agendamentos

### 8. Arcádia Fisco (Motor Fiscal)
**Arquivo:** `client/src/pages/Fisco.tsx`  
**API:** `/api/fisco/*`

Motor fiscal centralizado para compliance brasileiro:
- **NCM:** Nomenclatura Comum do Mercosul
- **CFOP:** Código Fiscal de Operações
- **CEST:** Código Especificador da Substituição Tributária
- **Grupos de Tributação:** Configuração de impostos
- **Certificados Digitais:** Gestão de A1/A3
- **NF-e/NFC-e:** Emissão de notas fiscais eletrônicas
- **IBS/CBS:** Campos para Reforma Tributária

#### Integração nfelib (Python)
**Arquivo:** `server/python/fisco_service.py`

Serviço FastAPI para processamento de NF-e:
- Geração de XML (layout 4.00)
- Assinatura digital com certificado A1
- Comunicação com SEFAZ (homologação/produção)
- Consulta, cancelamento e inutilização

### 9. Produção
**Arquivo:** `client/src/pages/Production.tsx`  
**API:** `/api/production/*`

Gestão de produção e manufatura:
- Ordens de produção
- Controle de estoque
- Rastreabilidade
- Custos de produção

### 10. Valuation (Precificação)
**Arquivo:** `client/src/pages/Valuation.tsx`  
**API:** `/api/valuation/*`

Sistema de precificação inteligente:
- Cálculo de custos
- Margem de contribuição
- Markup
- Simulações de preço

### 11. Suporte
**Arquivo:** `client/src/pages/Support.tsx`  
**API:** `/api/support/*`

Central de atendimento:
- Tickets de suporte
- Base de conhecimento
- SLA e prioridades
- Histórico de atendimentos

### 12. Automações
**Arquivo:** `client/src/pages/Automations.tsx`  
**API:** `/api/automations/*`

Motor de automações:
- Triggers e ações
- Workflows visuais
- Integrações via webhooks
- Agendamentos (cron)

### 13. Knowledge Base
**Arquivo:** `client/src/pages/Knowledge.tsx`  
**API:** `/api/knowledge/*`

Base de conhecimento:
- Artigos e documentação
- Categorização
- Busca semântica
- Integração com IA

### 14. IDE
**Arquivo:** `client/src/pages/IDE.tsx`  
**API:** `/api/ide/*`

Ambiente de desenvolvimento integrado:
- Monaco Editor
- Terminal (Xterm.js)
- Execução de código
- Gerenciamento de arquivos

### 15. Administração
**Arquivo:** `client/src/pages/Admin.tsx`  
**API:** `/api/admin/*`

Painel administrativo:
- **Usuários:** Gestão de contas
- **Perfis:** Controle de acesso
- **Parceiros:** Hierarquia multi-tenant
- **Módulos:** Configuração de funcionalidades
- **Configurações:** Parâmetros do sistema

### 16. API Hub
**Arquivo:** `client/src/pages/ApiHub.tsx`

Documentação interativa de APIs:
- Listagem de endpoints
- Testes em tempo real
- Exemplos de uso
- Geração de código

---

## Modelo de Dados

### Entidades Principais

#### Usuários e Autenticação
```sql
users               -- Usuários do sistema
profiles            -- Perfis de acesso
roles               -- Papéis (RBAC)
permissions         -- Permissões granulares
role_permissions    -- Associação papel-permissão
user_roles          -- Associação usuário-papel
module_access       -- Controle de acesso a módulos
```

#### Produtividade
```sql
workspace_pages     -- Páginas estilo Notion
page_blocks         -- Blocos de conteúdo
page_links          -- Links bidirecionais
dashboard_widgets   -- Widgets do dashboard
quick_notes         -- Notas rápidas
activity_feed       -- Feed de atividades
user_favorites      -- Favoritos
command_history     -- Histórico de comandos
```

#### Conversação e IA
```sql
conversations       -- Conversas com agente
messages            -- Mensagens
chat_attachments    -- Anexos
knowledge_base      -- Base de conhecimento
```

#### ERP e Integrações
```sql
erp_connections     -- Conexões com ERPs
agent_tasks         -- Tarefas do agente
task_executions     -- Execuções de tarefas
```

#### Comunicação
```sql
chat_threads        -- Threads de chat
chat_participants   -- Participantes
chat_messages       -- Mensagens de chat
whatsapp_sessions   -- Sessões WhatsApp
whatsapp_contacts   -- Contatos WhatsApp
whatsapp_messages   -- Mensagens WhatsApp
whatsapp_queues     -- Filas de atendimento
whatsapp_tickets    -- Tickets de atendimento
```

#### Process Compass
```sql
compass_clients         -- Clientes
compass_projects        -- Projetos
compass_project_members -- Membros de projeto
compass_project_phases  -- Fases de projeto
compass_contracts       -- Contratos
compass_timesheet       -- Timesheet
compass_invoices        -- Faturas
compass_payments        -- Pagamentos
```

#### CRM
```sql
crm_leads           -- Leads
crm_opportunities   -- Oportunidades
crm_activities      -- Atividades
crm_pipelines       -- Pipelines
crm_stages          -- Estágios
crm_commissions     -- Comissões
```

#### Fisco
```sql
fisco_ncm               -- NCMs
fisco_cest              -- CESTs
fisco_cfop              -- CFOPs
fisco_grupos_tributacao -- Grupos de tributação
fisco_natureza_operacao -- Naturezas de operação
fisco_ibpt              -- Tabela IBPT
fisco_certificados      -- Certificados digitais
fisco_configuracoes     -- Configurações fiscais
fisco_notas             -- Notas fiscais
fisco_nota_itens        -- Itens das notas
fisco_nota_eventos      -- Eventos fiscais
```

#### Multi-Tenant
```sql
partners            -- Parceiros
partner_invites     -- Convites de parceiros
tenant_clients      -- Clientes dos tenants
```

---

## APIs e Endpoints

### Estrutura Base

| Módulo | Base URL | Descrição |
|--------|----------|-----------|
| Admin | `/api/admin` | Administração do sistema |
| Compass | `/api/compass` | Process Compass |
| CRM | `/api/crm` | Gestão de relacionamento |
| WhatsApp | `/api/whatsapp` | Comunicação WhatsApp |
| Fisco | `/api/fisco` | Motor fiscal |
| BI | `/api/bi` | Business Intelligence |
| Production | `/api/production` | Gestão de produção |
| Valuation | `/api/valuation` | Precificação |
| Support | `/api/support` | Central de suporte |
| Automations | `/api/automations` | Automações |
| IDE | `/api/ide` | Ambiente de desenvolvimento |
| Learning | `/api/learning` | Sistema de aprendizado |

### Exemplos de Endpoints

#### Fisco - NF-e
```
GET    /api/fisco/nfe/service-status     # Status do serviço
POST   /api/fisco/nfe/validar-certificado # Validar certificado A1
POST   /api/fisco/nfe/gerar-xml          # Gerar XML preview
POST   /api/fisco/nfe/emitir             # Emitir NF-e
POST   /api/fisco/nfe/consultar          # Consultar na SEFAZ
POST   /api/fisco/nfe/cancelar           # Cancelar NF-e
POST   /api/fisco/nfe/inutilizar         # Inutilizar numeração
```

#### Compass - Clientes
```
GET    /api/compass/clients              # Listar clientes
GET    /api/compass/clients/:id          # Detalhes do cliente
POST   /api/compass/clients              # Criar cliente
PUT    /api/compass/clients/:id          # Atualizar cliente
DELETE /api/compass/clients/:id          # Excluir cliente
```

#### Admin - Usuários
```
GET    /api/admin/users                  # Listar usuários
GET    /api/admin/users/:id              # Detalhes do usuário
POST   /api/admin/users                  # Criar usuário
PUT    /api/admin/users/:id              # Atualizar usuário
DELETE /api/admin/users/:id              # Excluir usuário
```

---

## Integrações Externas

### OpenAI API
- **Uso:** Agente de IA, Scientist, auto-replies
- **Modelo:** gpt-4o-mini
- **Configuração:** Via Replit Secrets

### Baileys (WhatsApp)
- **Uso:** Conexão multi-sessão WhatsApp
- **Recursos:** QR Code, mensagens em tempo real
- **Armazenamento:** Sessões no banco de dados

### nfelib (Python)
- **Uso:** Emissão de NF-e/NFC-e
- **Recursos:** XML, assinatura digital, SEFAZ
- **Certificados:** A1 (PFX)

### Frappe Framework
- **Uso:** Arcádia Next (futuro)
- **Recursos:** ERPNext integration

### Google Calendar
- **Uso:** Sincronização de eventos CRM
- **OAuth:** Configurável por usuário

---

## Segurança e Autenticação

### Autenticação
- **Método:** Session-based com Passport.js
- **Hash:** bcrypt para senhas
- **Sessões:** PostgreSQL session store

### Controle de Acesso (RBAC)
```
Hierarquia:
├── Master (Arcádia)
│   └── Parceiros
│       └── Clientes
```

### Permissões
- Baseadas em módulos e ações
- Código formato: `modulo.recurso.acao`
- Exemplo: `compass.clients.write`

### Certificados Digitais
- Tipo A1 (arquivo PFX)
- Armazenamento seguro com senha
- Validação de expiração

---

## Guia de Implantação

### Requisitos
- Node.js 20+
- Python 3.11+
- PostgreSQL 15+
- Certificado SSL (produção)

### Variáveis de Ambiente
```env
DATABASE_URL=postgresql://...
SESSION_SECRET=...
OPENAI_API_KEY=...
FISCO_PYTHON_URL=http://localhost:8002
FISCO_PORT=8002
```

### Comandos de Inicialização
```bash
# Instalar dependências
npm install

# Iniciar em desenvolvimento
npm run dev

# Serviço Python Fisco (separado)
cd server/python && python fisco_service.py
```

### Portas
| Serviço | Porta |
|---------|-------|
| Frontend + API | 5000 |
| Python Fisco | 8002 |
| Python IA | 8001 |

---

## Changelog

### Janeiro 2026
- Integração nfelib para NF-e
- Módulo Fisco completo
- Sistema de aprendizado automático
- Validação Zod em todas as rotas fiscais

---

**Superstore** - Transformando a gestão empresarial com Inteligência Artificial

*Documentação gerada automaticamente pelo sistema.*

# PLANO ESTRATÉGICO DE EVOLUÇÃO
## Superstore → Frappe Framework
### Versão 1.0 - Janeiro 2026

---

## 1. VISÃO GERAL

### 1.1 Objetivo
Evoluir o Superstore para um **Business Operating System** completo, inspirado em três referências:

| Referência | O que Inspira |
|------------|---------------|
| **Notion** | Blocos modulares, banco de dados relacional, personalização |
| **Replit** | IDE no navegador, colaboração em tempo real, deploy instantâneo |
| **Discord** | Comunidades, canais contextuais, comunicação em tempo real |

### 1.2 Estratégia de Migração: Strangler Fig

A estratégia Strangler Fig permite:
- Manter WhatsApp, Manus, CRM funcionando durante toda a migração
- Construir o novo sistema em paralelo
- Migrar módulo a módulo até o sistema legado "desaparecer"

```
Sistema Atual (Express/React) continua funcionando
         ↓
Você vai adicionando "camadas Frappe" por cima
         ↓
Cada módulo migrado substitui o antigo
         ↓
No final, o "núcleo antigo" sumiu naturalmente
```

### 1.3 Stack Técnico

| Camada | Atual | Futuro |
|--------|-------|--------|
| Frontend | React 18 + TypeScript | Frappe Desk + React |
| Backend | Express.js | Frappe Framework |
| Database | PostgreSQL | PostgreSQL (mesmo) |
| Real-time | Socket.IO | Frappe Realtime + Socket.IO |
| WhatsApp | Baileys | Frappe App (Baileys) |
| IDE | Monaco + Terminal | IDE 3 Modos |

---

## 2. ESTRUTURA MULTI-TENANT

### 2.1 Hierarquia de 3 Níveis

```
NÍVEL 1: MASTER (Arcádia)
═════════════════════════
• Equipe de desenvolvimento
• Acesso total ao sistema
• IDE Pro-Code completa
• Central de Bibliotecas (publica apps)
• Suporte N3 (acessa tenants para debug)
• Gerencia parceiros e planos
         │
         ├───────────────────────┬───────────────────────┐
         ▼                       ▼                       ▼
NÍVEL 2: PARCEIROS
══════════════════
• Consultorias, integradores, revendas
• IDE Low-Code
• Gerencia seus clientes
• Comissões sobre vendas
• Suporte N2 aos clientes
• Baixa apps da biblioteca
         │
    ┌────┴────┐
    ▼         ▼
NÍVEL 3: CLIENTES
═════════════════
• Empresas usuárias finais
• Cockpit personalizado
• CRM/ERP operacional
• WhatsApp (N sessões conforme plano)
• BI próprio
• Manus com tools básicas
```

### 2.2 Matriz de Permissões por Tipo de Tenant

| Módulo | Master | Parceiro | Cliente |
|--------|--------|----------|---------|
| **IDE Pro-Code** | ✅ | ❌ | ❌ |
| **IDE Low-Code** | ✅ | ✅ | ❌ |
| **IDE No-Code** | ✅ | ✅ | ✅ (se habilitado) |
| **Central de Bibliotecas** | ✅ Publicar | ✅ Baixar | ❌ |
| **Central de APIs** | ✅ Gerenciar | ⚠️ Seus conectores | ⚠️ Leitura |
| **WhatsApp** | ✅ Ilimitado | ✅ N sessões | ✅ N sessões |
| **CRM/ERP** | ✅ Global | ✅ Próprio | ✅ Próprio |
| **Manus (IA)** | ✅ Todas tools | ✅ Tools permitidas | ✅ Básicas |
| **BI/Relatórios** | ✅ Global | ✅ Próprio | ✅ Próprio |
| **Suporte N3** | ✅ Acessa tenants | ❌ | ❌ |
| **Ver Parceiros** | ✅ | ✅ Seus clientes | ❌ |
| **Comissões** | ✅ Gerencia | ✅ Visualiza suas | ❌ |

### 2.3 Alterações no Schema

```sql
-- Alterações na tabela tenants
ALTER TABLE tenants ADD COLUMN tenant_type TEXT DEFAULT 'client';
-- master = Arcádia, partner = Parceiros, client = Clientes

ALTER TABLE tenants ADD COLUMN parent_tenant_id INTEGER REFERENCES tenants(id);
-- Referência ao tenant pai (hierarquia)

ALTER TABLE tenants ADD COLUMN partner_code TEXT;
-- Código do parceiro para rastreamento

ALTER TABLE tenants ADD COLUMN max_users INTEGER DEFAULT 5;
ALTER TABLE tenants ADD COLUMN max_storage_mb INTEGER DEFAULT 1000;
ALTER TABLE tenants ADD COLUMN features JSONB;
ALTER TABLE tenants ADD COLUMN commission_rate NUMERIC(5,2);
ALTER TABLE tenants ADD COLUMN trial_ends_at TIMESTAMP;

-- Nova tabela: Planos
CREATE TABLE tenant_plans (
  id SERIAL PRIMARY KEY,
  code TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  tenant_type TEXT NOT NULL,
  max_users INTEGER DEFAULT 5,
  max_storage_mb INTEGER DEFAULT 1000,
  features JSONB,
  monthly_price INTEGER DEFAULT 0,
  yearly_price INTEGER DEFAULT 0,
  is_active TEXT DEFAULT 'true',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Nova tabela: Relacionamento Parceiro-Cliente
CREATE TABLE partner_clients (
  id SERIAL PRIMARY KEY,
  partner_id INTEGER NOT NULL REFERENCES tenants(id),
  client_id INTEGER NOT NULL REFERENCES tenants(id),
  commission_rate NUMERIC(5,2),
  status TEXT DEFAULT 'active',
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ended_at TIMESTAMP
);

-- Nova tabela: Comissões
CREATE TABLE partner_commissions (
  id SERIAL PRIMARY KEY,
  partner_id INTEGER NOT NULL REFERENCES tenants(id),
  client_id INTEGER NOT NULL REFERENCES tenants(id),
  reference_month TEXT NOT NULL,
  client_plan_value INTEGER NOT NULL,
  commission_rate NUMERIC(5,2) NOT NULL,
  commission_value INTEGER NOT NULL,
  status TEXT DEFAULT 'pending',
  paid_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 3. CRONOGRAMA GERAL (6-9 Meses)

```
         Mês 1      Mês 2      Mês 3      Mês 4      Mês 5      Mês 6+
         ├──────────┼──────────┼──────────┼──────────┼──────────┼────────►

FASE 0   ████████████████████
         FUNDAÇÃO
         Setup + Tenants + SSO

FASE 1              ████████████████████████████
                    INFRAESTRUTURA
                    CRM/ERP + Central APIs + Manus

FASE 2                         ████████████████████████████████
                               EXPERIÊNCIA
                               Cockpit + Comunidades + IDE

FASE 3                                              ████████████████████►
                                                    AUTOMAÇÃO
                                                    WhatsApp + RPA + Decommission
```

---

## 4. FASE 0: FUNDAÇÃO (Semanas 1-8)

### 4.1 Objetivo
Preparar a base técnica sem quebrar nada do sistema atual.

### 4.2 Entregas

| # | Entrega | Descrição | Semana |
|---|---------|-----------|--------|
| 0.1 | **Setup Frappe Bench** | Instalar Frappe em servidor paralelo | 1-2 |
| 0.2 | **Hierarquia de Tenants** | Novos campos e tabelas no PostgreSQL | 2-3 |
| 0.3 | **SSO Bridge** | Login unificado (usuário loga uma vez) | 3-4 |
| 0.4 | **CDC Pipeline** | Sincronização de dados PostgreSQL ↔ Frappe | 4-6 |
| 0.5 | **Vault de Secrets** | Gerenciamento seguro de API keys | 5-6 |
| 0.6 | **Feature Flags** | Sistema de features por plano/tenant | 6-7 |
| 0.7 | **Planos e Preços** | Tabela de planos (free, starter, pro, enterprise) | 7-8 |

### 4.3 Resultado
- ✅ Frappe rodando em paralelo
- ✅ Hierarquia master/partner/client funcionando
- ✅ Login único nos dois sistemas
- ✅ Dados sincronizados em tempo real
- ✅ Planos e features configuráveis

---

## 5. FASE 1: INFRAESTRUTURA (Semanas 6-16)

### 5.1 Objetivo
Migrar dados mestres e criar a Central de APIs.

### 5.2 Entregas

| # | Entrega | Descrição | Semana |
|---|---------|-----------|--------|
| 1.1 | **DocTypes CRM** | Clientes, Leads, Oportunidades no Frappe | 6-8 |
| 1.2 | **DocTypes ERP** | Produtos, Pedidos, Faturas no Frappe | 8-10 |
| 1.3 | **Central de APIs (MVP)** | Dashboard visual de integrações | 9-12 |
| 1.4 | **Conectores Básicos** | Interface para SEFAZ, Bancos (dados demo) | 12-14 |
| 1.5 | **Manus Frappe** | Agente IA via background jobs | 13-15 |
| 1.6 | **Knowledge Graph** | Migração do grafo para DocTypes | 14-16 |

### 5.3 Central de APIs - Detalhamento

**IMPORTANTE:** A Central de APIs é uma interface visual de gerenciamento. Os dados de integrações (SEFAZ, Bancos, Mercado Livre) são ILUSTRATIVOS/DEMO. Não fazemos integração real com APIs externas nesta fase.

O que construímos:
- ✅ Interface visual (React)
- ✅ CRUD de conectores (cadastrar, editar, remover)
- ✅ Status visual (online, warning, error)
- ✅ Logs fictícios para demonstração
- ✅ Configurações por conector
- ✅ Permissões por tenant type

O que NÃO fazemos:
- ❌ Conectar à SEFAZ real
- ❌ Conectar a bancos reais
- ❌ Chamadas API externas

### 5.4 Resultado
- ✅ CRM/ERP acessível via Frappe Desk
- ✅ Central de APIs funcionando (dados demo)
- ✅ Manus consultando dados do Frappe
- ✅ Knowledge Graph migrado

---

## 6. FASE 2: EXPERIÊNCIA (Semanas 12-24)

### 6.1 Objetivo
Construir a nova interface (Cockpit, Comunidades, IDE).

### 6.2 Entregas

| # | Entrega | Descrição | Semana |
|---|---------|-----------|--------|
| 2.1 | **Cockpit PARA** | Navegação Projetos/Áreas/Recursos/Arquivo | 12-15 |
| 2.2 | **Dashboard Tríade** | Importante/Urgente/Circunstancial | 14-16 |
| 2.3 | **Widgets Sistema** | Tarefas, Calendário, Gráficos | 15-17 |
| 2.4 | **Comunidades MVP** | Canais por projeto (Socket.IO via Frappe) | 16-19 |
| 2.5 | **IDE No-Code** | DocType Builder visual | 18-20 |
| 2.6 | **IDE Low-Code** | Templates de scripts | 20-22 |
| 2.7 | **IDE Pro-Code** | Monaco + Terminal + Live Preview | 21-23 |
| 2.8 | **Central de Bibliotecas** | Repositório de apps Frappe | 22-24 |

### 6.3 Cockpit PARA + Tríade

O Cockpit é a interface principal do usuário, baseado em duas metodologias:

**Método PARA (Tiago Forte):**
- **P**rojetos: Todos os projetos ativos com metas e prazos
- **Á**reas: Áreas de responsabilidade contínua (Vendas, Financeiro, RH)
- **R**ecursos: Base de conhecimento, templates, manuais
- **A**rquivo: Tudo concluído ou inativo, para consulta futura

**Tríade do Tempo (Christian Barbosa):**
- 🟢 **Importante** (70% do tempo): Atividades que geram valor
- 🟡 **Urgente** (20% do tempo): Atividades com prazo apertado
- 🔴 **Circunstancial** (10% do tempo): Atividades que não agregam

### 6.4 IDE 3 Modos

| Modo | Quem Usa | O que Faz |
|------|----------|-----------|
| **No-Code** | Clientes | Criar formulários arrastando, workflows visuais, relatórios com filtros |
| **Low-Code** | Parceiros | Server Scripts com templates, validações, webhooks, fórmulas |
| **Pro-Code** | Arcádia | Monaco Editor completo, Terminal, Git, Deploy de apps |

### 6.5 Resultado
- ✅ Cockpit PARA + Tríade funcionando
- ✅ Comunidades com canais por projeto
- ✅ IDE com 3 modos operando
- ✅ Central de Bibliotecas publicando apps

---

## 7. FASE 3: AUTOMAÇÃO E DECOMMISSION (Semana 20+)

### 7.1 Objetivo
Migrar serviços restantes e desligar o legado.

### 7.2 Entregas

| # | Entrega | Descrição | Semana |
|---|---------|-----------|--------|
| 3.1 | **WhatsApp Frappe App** | Reconstruir Baileys como app nativo | 20-24 |
| 3.2 | **Motor de Workflows** | Automações visuais (RPA) | 22-26 |
| 3.3 | **Scientist Frappe** | Migrar para Frappe Workers | 24-28 |
| 3.4 | **Validação de Paridade** | Testes A/B, métricas | 26-30 |
| 3.5 | **Decommission Express** | Desligar endpoints legados | 30+ |

### 7.3 Resultado
- ✅ Sistema 100% unificado no Frappe
- ✅ Express/React desligado
- ✅ Uma única plataforma para manter

---

## 8. MAPEAMENTO DE MÓDULOS

| Módulo Atual | O que Acontece | Fase |
|--------------|----------------|------|
| **users, tenants** | Expande com hierarquia | 0 |
| **profiles, roles, permissions** | Migra para Frappe RBAC | 0 |
| **whatsapp_contacts, messages, tickets** | Mantém → Migra na Fase 3 | 3 |
| **pc_crm_leads, stages, opportunities** | Migra para Frappe DocTypes | 1 |
| **pc_clients, projects, tasks** | Migra para Frappe DocTypes | 1 |
| **graph_nodes, graph_edges** | Migra para Frappe Knowledge Graph | 1 |
| **internal_chat_*** | Evolui para Comunidades | 2 |
| **manus_*** | Integra via background jobs | 1 |
| **bi_*** | Mantém + novos widgets Cockpit | 2 |
| **ide_*** | Evolui para 3 modos | 2 |

---

## 9. RISCOS E MITIGAÇÕES

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| **Drift de dados** | Média | Alto | CDC com validação contínua |
| **Performance chat** | Média | Médio | Load test antes de migrar |
| **Tokens WhatsApp** | Baixa | Alto | Vault de secrets |
| **Curva aprendizado Frappe** | Alta | Médio | Treinamento na Fase 0 |
| **Regressões funcionais** | Média | Alto | Testes A/B, telemetria |
| **Resistência usuários** | Média | Médio | Piloto gradual: Master → Partners → Clients |

---

## 10. QUICK WINS (Entregas Rápidas)

| Item | Tempo | Valor |
|------|-------|-------|
| **Hierarquia de Tenants** | 2 semanas | Estrutura para parceiros |
| **SSO unificado** | 2 semanas | Login único |
| **Central de APIs (UI)** | 3 semanas | Visibilidade integrações |
| **Dashboard Tríade** | 2 semanas | Consciência sobre tempo |
| **Planos e Features** | 2 semanas | Monetização estruturada |

---

## 11. OS 5 PILARES DO SISTEMA

### Pilar 1: Knowledge Graph
- Todos os dados do negócio conectados e pesquisáveis
- Navegação visual entre entidades relacionadas
- Base para IA contextual

### Pilar 2: Central Intelligence (Scientist)
- IA que aprende com interações do sistema
- Gera e executa código automaticamente
- Detecta padrões e sugere otimizações

### Pilar 3: Autonomous Agent (Manus)
- Executa tarefas multi-step de forma autônoma
- Acessa ferramentas e APIs
- Deep research com planejamento

### Pilar 4: Unified Communication
- WhatsApp integrado com CRM
- Chat interno com canais por projeto
- Email (futuro)
- Todos os canais em um lugar

### Pilar 5: Complete IDE
- 3 modos de desenvolvimento (No/Low/Pro Code)
- Central de Bibliotecas
- Deploy integrado

---

## 12. DOCUMENTOS DE REFERÊNCIA

Os documentos originais que basearam este plano estão em:
- `attached_assets/cocpti_docs/cocpti/` - Cockpit e DNA Notion
- `attached_assets/cocpti_docs/Ide Arcadia/` - Proposta IDE
- `attached_assets/cocpti_docs/Rota de desenvolviento/` - Roadmap original
- `attached_assets/cocpti_docs/Central de API/` - Central de APIs

---

## 13. PRÓXIMOS PASSOS

1. [ ] Implementar hierarquia de tenants no schema
2. [ ] Criar tabelas de planos e comissões
3. [ ] Documentar arquitetura CDC
4. [ ] Provisionar servidor Frappe
5. [ ] Implementar Central de APIs (UI com dados demo)
6. [ ] Construir Cockpit PARA + Tríade

---

*Documento criado em Janeiro 2026*
*Última atualização: Janeiro 2026*

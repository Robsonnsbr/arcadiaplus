# ControlPLUS - Sistema de Gestão Empresarial

## Preferências do Usuário

- Idioma de comunicação: **Português do Brasil (pt-BR)**

## Overview

ControlPLUS é um ERP/sistema de gestão Laravel 10 PHP (Superstore). O diretório `arcadiasuite/` é um app separado que **não está sendo executado**.

## Active Application

**ControlPLUS** — Laravel 10 PHP ERP rodando na porta 5000.

## Tech Stack

- **Framework**: Laravel 10 (PHP 8.2)
- **Frontend**: Bootstrap 5 + Vite (assets pré-compilados em `public/build/`)
- **Database**: PostgreSQL (schema: `controlplus`)
- **ORM**: Eloquent

## Project Structure

- `ControlPLUS/` — Aplicação Laravel ativa
  - `app/` — Models, Controllers, Services
  - `resources/views/` — Templates Blade
  - `public/build/` — Assets Vite pré-compilados
  - `database/migrations/` — Migrations Laravel
  - `database/dump.sql` — Dump original MySQL (11.608 linhas, 306 tabelas, 50 INSERTs)
  - `.env` — Configurações de ambiente (DB, app)

## Setup & Run

```bash
cd ControlPLUS
composer install
php artisan key:generate
php artisan migrate --force
npm install && npm run build
php artisan serve --host=0.0.0.0 --port=5000
```

## Database

- Connection: PostgreSQL em `helium:5432`
- Database: `heliumdb`
- Schema: `controlplus` (isolado do arcadiasuite)
- Credentials: armazenadas em `.env`
- **306 tabelas importadas** com todos os dados de produção
- 5.570 cidades, 2 empresas, 4 usuários importados

### Usuários do sistema (importados do dump)

| Email | Papel |
|-------|-------|
| controlplus@master.com | master |
| superstore@bcprime.inf.br | superstore |
| financeiro@superstoreeletronicos.com.br | adm |
| luan@superstoreeletronicos.com.br | Luan Rosa |

## Database Import (MySQL → PostgreSQL)

O dump original (`database/dump.sql`) é formato MySQL 8.0. Para recriar o banco:

1. Rodar o conversor Python: `python3 scripts/convert_dump.py`
2. Importar: `PGPASSWORD=password psql -h helium -U postgres -d heliumdb -f /tmp/dump_pg_v2.sql`
3. Atualizar sequences: ver script PL/pgSQL em `.local/sequences.sql`
4. Rodar migrations: `php artisan migrate --force`

### Conversões MySQL → PostgreSQL aplicadas

- Backticks → sem aspas (ou aspas duplas para palavras reservadas)
- `AUTO_INCREMENT` → `SERIAL`/`BIGSERIAL`
- `BIGINT UNSIGNED` → `BIGINT`
- `TINYINT(1)` → `SMALLINT`
- `DATETIME` → `TIMESTAMP`
- `LONGTEXT`/`MEDIUMTEXT` → `TEXT`
- `ENUM(...)` → `VARCHAR(255)`
- `\'` (escape MySQL) → `''` (escape PostgreSQL padrão)
- `COLLATE utf8mb4_unicode_ci` → removido
- `ENGINE=InnoDB` → removido
- Foreign Keys removidas do CREATE TABLE (para permitir importação fora de ordem)
- `session_replication_role = replica` durante importação

## Key Notes

- Arquivos de migration adaptados para compatibilidade PostgreSQL
- Assets frontend pré-compilados (não usa Vite dev server)
- `arcadiasuite/` não está sendo executado
- Sequências PostgreSQL atualizadas após importação do dump

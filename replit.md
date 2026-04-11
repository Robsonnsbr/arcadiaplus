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
- **Database**: MySQL 8.0 (local, socket `/tmp/mysql.sock`)
- **ORM**: Eloquent

## Project Structure

- `ControlPLUS/` — Aplicação Laravel ativa
  - `app/` — Models, Controllers, Services
  - `resources/views/` — Templates Blade
  - `public/build/` — Assets Vite pré-compilados
  - `database/migrations/` — Migrations Laravel
  - `database/dump.sql` — Dump original MySQL (306 tabelas, dados de produção)
  - `.env` — Configurações de ambiente (DB, app)
- `start.sh` — Script de inicialização (sobe MySQL + Laravel)

## Setup & Run

O workflow `Start application` executa `bash start.sh`, que:
1. Inicia o MySQL 8.0 em background (`/home/runner/mysql-data`)
2. Aguarda o MySQL ficar disponível
3. Cria banco/usuário se necessário
4. Importa o dump de produção se banco vazio
5. Roda migrations pendentes
6. Inicia `php artisan serve --host=0.0.0.0 --port=5000`

## Database

- Connection: MySQL 8.0 local via socket `/tmp/mysql.sock`
- Database: `controlplus`
- Username: `controlplus` / Password: `controlplus`
- Dados em: `/home/runner/mysql-data/`
- **306 tabelas importadas** com todos os dados de produção
- 5.570 cidades, 2 empresas, 4 usuários

### Usuários do sistema (importados do dump)

| Email | Papel |
|-------|-------|
| controlplus@master.com | master |
| superstore@bcprime.inf.br | superstore |
| financeiro@superstoreeletronicos.com.br | adm |
| luan@superstoreeletronicos.com.br | Luan Rosa |

## Key Notes

- MySQL 8.0 instalado via Nix (`mysql80`)
- O dump `database/dump.sql` é importado diretamente no MySQL (sem conversão)
- A variável `DATABASE_URL` do Replit (PostgreSQL) foi neutralizada removendo `'url' => env('DATABASE_URL')` do bloco `mysql` em `config/database.php`
- Assets frontend pré-compilados (não usa Vite dev server)
- `arcadiasuite/` não está sendo executado

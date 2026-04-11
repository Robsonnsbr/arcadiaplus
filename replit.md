# ControlPLUS - Sistema de Gestão Empresarial

## Preferências do Usuário

- Idioma de comunicação: **Português do Brasil (pt-BR)**

## Overview

ControlPLUS is a Laravel 10 PHP ERP/management system (Superstore). The `arcadiasuite/` directory is a separate companion app that is currently not being run.

## Active Application

**ControlPLUS** — Laravel 10 PHP ERP running at port 5000.

## Tech Stack

- **Framework**: Laravel 10 (PHP 8.2)
- **Frontend**: Bootstrap 5 + Vite (pre-built assets)
- **Database**: PostgreSQL (schema: `controlplus`)
- **ORM**: Eloquent

## Project Structure

- `ControlPLUS/` — Active Laravel application
  - `app/` — Models, Controllers, Services
  - `resources/views/` — Blade templates
  - `public/build/` — Pre-built Vite assets
  - `database/migrations/` — Laravel migrations
  - `.env` — Environment config (DB, app settings)

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

- Connection: PostgreSQL at `helium:5432`
- Database: `heliumdb`
- Schema: `controlplus` (isolated from arcadiasuite)
- Credentials: stored in `.env`

## Key Notes

- Migration files were adapted for PostgreSQL compatibility (MySQL-specific ENUM/index syntax replaced)
- Frontend assets are pre-built (not using Vite dev server)
- `arcadiasuite/` companion app is NOT being run

#!/bin/bash
set -e

echo "🚀 Rodando Drizzle migrations..."

if [ -z "$DATABASE_URL" ]; then
  echo "❌ DATABASE_URL não definido"
  exit 1
fi

npx drizzle-kit generate --config drizzle.config.ts
npx drizzle-kit migrate --config drizzle.config.ts

echo "✅ Migrations concluídas com sucesso"

#!/usr/bin/env bash
set -e

MYSQL_DATA="/home/runner/mysql-data"
MYSQL_SOCK="/tmp/mysql.sock"

echo "=== Iniciando MySQL ==="

# Inicializa o diretório de dados se necessário
if [ ! -d "$MYSQL_DATA/mysql" ]; then
    echo "Inicializando diretório de dados MySQL..."
    mysqld --initialize-insecure --datadir="$MYSQL_DATA" --user="$(whoami)" 2>/dev/null
fi

# Inicia o MySQL em background
mysqld \
    --datadir="$MYSQL_DATA" \
    --socket="$MYSQL_SOCK" \
    --pid-file=/tmp/mysql.pid \
    --port=3306 \
    --mysqlx=OFF \
    --log-error=/tmp/mysqld.log \
    --character-set-server=utf8mb4 \
    --collation-server=utf8mb4_unicode_ci \
    --sql-mode="STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION" &

echo "Aguardando MySQL ficar disponível..."
for i in $(seq 1 30); do
    if mysqladmin -u root -S "$MYSQL_SOCK" ping --silent 2>/dev/null; then
        echo "MySQL disponível!"
        break
    fi
    sleep 1
done

# Configura banco e usuário (ignora se já existir)
echo "Configurando banco de dados..."
mysql -u root -S "$MYSQL_SOCK" 2>/dev/null <<'SQL'
CREATE DATABASE IF NOT EXISTS controlplus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'controlplus'@'localhost' IDENTIFIED BY 'controlplus';
GRANT ALL PRIVILEGES ON controlplus.* TO 'controlplus'@'localhost';
FLUSH PRIVILEGES;
SQL

# Verifica se o banco já foi importado
TABLE_COUNT=$(mysql -u controlplus -pcontrolplus controlplus -S "$MYSQL_SOCK" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='controlplus';" -s -N 2>/dev/null || echo 0)

if [ "$TABLE_COUNT" -lt "10" ]; then
    echo "Banco vazio — importando dump e rodando migrations..."
    cd ControlPLUS
    php artisan migrate:fresh --seed --force 2>/dev/null || true

    if [ -f "database/dump.sql" ]; then
        echo "Importando dump de produção..."
        mysql -u controlplus -pcontrolplus controlplus -S "$MYSQL_SOCK" < database/dump.sql
        echo "Dump importado com sucesso."
        php artisan migrate --force 2>/dev/null || true
    fi
    cd ..
else
    echo "Banco já configurado ($TABLE_COUNT tabelas). Rodando migrations pendentes..."
    cd ControlPLUS
    php artisan migrate --force 2>/dev/null || true
    cd ..
fi

echo "=== Iniciando Laravel ==="
cd ControlPLUS
exec php artisan serve --host=0.0.0.0 --port=5000

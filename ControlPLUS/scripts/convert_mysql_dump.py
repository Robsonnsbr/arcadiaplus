#!/usr/bin/env python3
"""
Conversor de dump MySQL para PostgreSQL para o ControlPLUS.

Uso:
    python3 scripts/convert_mysql_dump.py [input] [output]

Padrão:
    input: database/dump.sql
    output: /tmp/dump_pg_v2.sql

Depois de converter:
    PGPASSWORD=password psql -h helium -U postgres -d heliumdb -f /tmp/dump_pg_v2.sql
"""

import re
import sys
import os

INPUT = sys.argv[1] if len(sys.argv) > 1 else os.path.join(os.path.dirname(__file__), '..', 'database', 'dump.sql')
OUTPUT = sys.argv[2] if len(sys.argv) > 2 else '/tmp/dump_pg_v2.sql'


def convert_column_type(t):
    t = re.sub(r'bigint unsigned\s+not null\s+auto_increment', 'bigserial NOT NULL', t, flags=re.IGNORECASE)
    t = re.sub(r'int unsigned\s+not null\s+auto_increment', 'serial NOT NULL', t, flags=re.IGNORECASE)
    t = re.sub(r'bigint\s+not null\s+auto_increment', 'bigserial NOT NULL', t, flags=re.IGNORECASE)
    t = re.sub(r'int\s+not null\s+auto_increment', 'serial NOT NULL', t, flags=re.IGNORECASE)
    t = re.sub(r'\bauto_increment\b', '', t, flags=re.IGNORECASE)
    t = re.sub(r'bigint unsigned', 'bigint', t, flags=re.IGNORECASE)
    t = re.sub(r'int unsigned', 'integer', t, flags=re.IGNORECASE)
    t = re.sub(r'tinyint\s*\(\s*1\s*\)', 'smallint', t, flags=re.IGNORECASE)
    t = re.sub(r'tinyint\s*\(\s*\d+\s*\)', 'smallint', t, flags=re.IGNORECASE)
    t = re.sub(r'\btinyint\b', 'smallint', t, flags=re.IGNORECASE)
    t = re.sub(r'smallint\s*\(\s*\d+\s*\)', 'smallint', t, flags=re.IGNORECASE)
    t = re.sub(r'mediumint\s*\(\s*\d+\s*\)', 'integer', t, flags=re.IGNORECASE)
    t = re.sub(r'\bmediumint\b', 'integer', t, flags=re.IGNORECASE)
    t = re.sub(r'\bint\s*\(\s*\d+\s*\)', 'integer', t, flags=re.IGNORECASE)
    t = re.sub(r'\binteger\s*\(\s*\d+\s*\)', 'integer', t, flags=re.IGNORECASE)
    t = re.sub(r'bigint\s*\(\s*\d+\s*\)', 'bigint', t, flags=re.IGNORECASE)
    t = re.sub(r'\bdatetime\b', 'timestamp', t, flags=re.IGNORECASE)
    t = re.sub(r'\blongtext\b', 'text', t, flags=re.IGNORECASE)
    t = re.sub(r'\bmediumtext\b', 'text', t, flags=re.IGNORECASE)
    t = re.sub(r'\btinytext\b', 'text', t, flags=re.IGNORECASE)
    t = re.sub(r'\blongblob\b', 'bytea', t, flags=re.IGNORECASE)
    t = re.sub(r'\bmediumblob\b', 'bytea', t, flags=re.IGNORECASE)
    t = re.sub(r'\btinyblob\b', 'bytea', t, flags=re.IGNORECASE)
    t = re.sub(r'\bblob\b', 'bytea', t, flags=re.IGNORECASE)
    t = re.sub(r'\bdouble\b', 'double precision', t, flags=re.IGNORECASE)
    t = re.sub(r"\benum\s*\([^)]+\)", "varchar(255)", t, flags=re.IGNORECASE)
    t = re.sub(r"\bset\s*\([^)]+\)", "varchar(255)", t, flags=re.IGNORECASE)
    t = re.sub(r'\s+COLLATE\s+[^\s,]+', '', t, flags=re.IGNORECASE)
    t = re.sub(r'\s+CHARACTER SET\s+[^\s,]+', '', t, flags=re.IGNORECASE)
    t = re.sub(r"DEFAULT b'0'", "DEFAULT 0", t, flags=re.IGNORECASE)
    t = re.sub(r"DEFAULT b'1'", "DEFAULT 1", t, flags=re.IGNORECASE)
    return t


def convert_identifiers(s):
    pg_reserved = {
        'user', 'order', 'group', 'table', 'column', 'check', 'index',
        'primary', 'key', 'value', 'values', 'default', 'select', 'from',
        'where', 'and', 'or', 'not', 'in', 'is', 'null', 'true', 'false',
        'create', 'drop', 'alter', 'insert', 'update', 'delete', 'set',
        'all', 'any', 'as', 'by', 'on', 'to', 'with', 'end', 'case',
        'when', 'then', 'else', 'for', 'references', 'constraint', 'unique',
        'foreign', 'returning'
    }

    def replace_backtick(m):
        name = m.group(1)
        # Colunas com letras maiúsculas ou palavras reservadas precisam de aspas duplas
        # para preservar o case no PostgreSQL
        if name.lower() in pg_reserved or name != name.lower():
            return f'"{name}"'
        return name

    return re.sub(r'`([^`]+)`', replace_backtick, s)


def convert_insert(line):
    line = convert_identifiers(line)
    # Converte escape de aspas simples do MySQL \' para padrão SQL ''
    line = re.sub(r"\\'", "''", line)
    # Converte \" (escape MySQL de aspas duplas em JSON) para " puro
    # O PostgreSQL com standard_conforming_strings=on não interpreta \"
    line = line.replace('\\"', '"')
    # Fix bit literals
    line = re.sub(r",b'(\d)'", r",\1", line)
    line = re.sub(r"\(b'(\d)'", r"(\1", line)
    return line


def process_create_table(block_str):
    block_lines = block_str.split('\n')
    new_lines = []
    for bl in block_lines:
        if re.match(r'\s*CONSTRAINT\s+\S+\s+FOREIGN KEY', bl, re.IGNORECASE):
            continue
        if re.match(r'\s*KEY\s+', bl, re.IGNORECASE) and not re.match(r'\s*(PRIMARY|UNIQUE|FOREIGN)\s+KEY', bl, re.IGNORECASE):
            continue
        bl = re.sub(r'\bUNIQUE KEY\s+`[^`]+`\s*', 'UNIQUE ', bl, flags=re.IGNORECASE)
        bl = re.sub(r'\bUNIQUE KEY\s+\S+\s*', 'UNIQUE ', bl, flags=re.IGNORECASE)
        bl = convert_column_type(bl)
        bl = convert_identifiers(bl)
        new_lines.append(bl)

    result = []
    for i, bl in enumerate(new_lines):
        if i < len(new_lines) - 1:
            next_stripped = new_lines[i + 1].strip()
            if (next_stripped == ');' or next_stripped.startswith(')')) and bl.rstrip().endswith(','):
                bl = bl.rstrip()[:-1]
        result.append(bl)
    return '\n'.join(result)


def main():
    with open(INPUT, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.read().split('\n')

    result_lines = []
    i = 0

    while i < len(lines):
        line = lines[i]

        if (re.match(r'\s*/\*!', line) or
                re.match(r'\s*SET @OLD_', line) or
                re.match(r'\s*SET NAMES', line) or
                re.match(r'\s*SET TIME_ZONE', line) or
                re.match(r'\s*(LOCK|UNLOCK)\s+TABLES', line, re.IGNORECASE)):
            i += 1
            continue

        if re.match(r'\s*DROP TABLE IF EXISTS', line, re.IGNORECASE):
            line = convert_identifiers(line)
            # Adiciona CASCADE para remover dependências (FK constraints)
            line = re.sub(r'(DROP TABLE IF EXISTS \S+);', r'\1 CASCADE;', line)
            result_lines.append(line)
            i += 1
            continue

        if re.match(r'\s*CREATE TABLE', line, re.IGNORECASE):
            block = []
            while i < len(lines):
                block.append(lines[i])
                stripped = lines[i].strip()
                if stripped.endswith(';') and (
                    stripped.startswith(')') or
                    re.match(r'\).*ENGINE.*', stripped, re.IGNORECASE) or
                    re.match(r'\).*DEFAULT.*', stripped, re.IGNORECASE)
                ):
                    i += 1
                    break
                if stripped == ');':
                    i += 1
                    break
                i += 1

            block_str = '\n'.join(block)
            block_str = re.sub(
                r'\)\s*(?:ENGINE\s*=\s*\S+\s*|DEFAULT\s+CHARSET\s*=\s*\S+\s*|COLLATE\s*=\s*\S+\s*|AUTO_INCREMENT\s*=\s*\d+\s*|ROW_FORMAT\s*=\s*\S+\s*|COMMENT\s*=\s*\'[^\']*\'\s*)*;',
                ');',
                block_str,
                flags=re.IGNORECASE
            )
            result_lines.append(process_create_table(block_str))
            continue

        if re.match(r'\s*INSERT INTO', line, re.IGNORECASE):
            result_lines.append(convert_insert(line))
            i += 1
            continue

        if line.strip().startswith('--') or line.strip() == '':
            result_lines.append(line)
            i += 1
            continue

        i += 1

    output = '\n'.join(result_lines)

    with open(OUTPUT, 'w', encoding='utf-8') as f:
        f.write('SET session_replication_role = replica;\n')
        f.write('SET search_path TO controlplus;\n\n')
        f.write(output)
        f.write('\n\nSET session_replication_role = origin;\n')

    print(f"Conversão concluída: {OUTPUT}")
    print(f"CREATE TABLEs: {output.count('CREATE TABLE')}")
    print(f"INSERT INTOs: {output.count('INSERT INTO')}")


if __name__ == '__main__':
    main()

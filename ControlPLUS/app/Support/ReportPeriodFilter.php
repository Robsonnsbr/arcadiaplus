<?php

namespace App\Support;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * Central helper for applying the standard period filter
 * (`start_date` / `end_date`, day-granularity) used by the reports.
 *
 * A single place keeps column choice, NULL handling and timezone
 * semantics consistent across reports, so UI presets like "Hoje"
 * and "Mês atual" always match what "Todo o período" would show.
 *
 * Column policy per report type:
 *
 *   | Relatório                 | Coluna usada no intervalo                    |
 *   |---------------------------|----------------------------------------------|
 *   | NF-e                      | COALESCE(data_emissao, created_at)           |
 *   | NFC-e                     | COALESCE(data_emissao, created_at)           |
 *   | Contas a pagar / receber  | data_vencimento                              |
 *   | Lançamentos financeiros   | data_vencimento (por tipo)                   |
 *   | Demais (vendas, estoque,  | created_at                                   |
 *   | clientes, pedidos, etc.)  |                                              |
 *
 * Use {@see coalesce()} for columns that can be NULL for records
 * still valid for the report (e.g. fiscal date set only after
 * authorization). Use a plain string column for operational
 * date columns that are always populated.
 */
class ReportPeriodFilter
{
    /**
     * Apply an inclusive [start, end] date range to $query using
     * day-granularity (matches the `YYYY-MM-DD` inputs from the UI).
     *
     * $column may be a simple column name or a raw SQL expression
     * (e.g. the {@see coalesce()} helper below) to cover columns that
     * can be NULL for records still valid for the report.
     */
    public static function apply($query, $column, ?string $start = null, ?string $end = null)
    {
        if (!empty($start)) {
            $query->whereDate($column, '>=', $start);
        }

        if (!empty($end)) {
            $query->whereDate($column, '<=', $end);
        }

        return $query;
    }

    /**
     * Build a `COALESCE(primary, fallback)` SQL expression so the
     * range is also satisfied when the primary column is NULL
     * (typical case: fiscal date columns that are only set after
     * authorization, like `data_emissao` on NF-e/NFC-e drafts).
     */
    public static function coalesce(string $primary, string $fallback = 'created_at'): Expression
    {
        return DB::raw("COALESCE({$primary}, {$fallback})");
    }
}

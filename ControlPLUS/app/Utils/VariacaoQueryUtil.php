<?php

namespace App\Utils;

class VariacaoQueryUtil
{
    public static function apply($query, $produto_variacao_id, string $column = 'produto_variacao_id')
    {
        return $query->when($produto_variacao_id != null, function ($q) use ($produto_variacao_id, $column) {
            return $q->where($column, $produto_variacao_id);
        }, function ($q) use ($column) {
            return $q->whereNull($column);
        });
    }
}


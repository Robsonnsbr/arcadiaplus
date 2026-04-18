<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Produto;

/**
 * Verifica que o SKU é único por empresa.
 *
 * Aceita um $ignoreId opcional para permitir que o produto atual
 * mantenha seu próprio SKU durante um update (sem false positive).
 *
 * Regras de segurança:
 * - Valor vazio/null passa sempre (campo nullable para legado).
 * - Apenas valores preenchidos são verificados contra o banco.
 */
class ValidaSkuUnico implements Rule
{
    protected ?int $empresaId;
    protected ?int $ignoreId;
    protected ?string $conflito = null;

    public function __construct(?int $empresaId, ?int $ignoreId = null)
    {
        $this->empresaId = $empresaId;
        $this->ignoreId  = $ignoreId;
    }

    public function passes($attribute, $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $query = Produto::where('empresa_id', $this->empresaId)
            ->where('sku', $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        $existente = $query->first();

        if ($existente) {
            $this->conflito = $existente->nome;
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->conflito
            ? "SKU já utilizado pelo produto \"{$this->conflito}\""
            : 'SKU já cadastrado para esta empresa';
    }
}

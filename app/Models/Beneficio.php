<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TipoBeneficio;

/**
 * @OA\Schema(
 *     schema="Beneficio",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nome", type="string", example="Vale Refeição"),
 *     @OA\Property(property="descricao", type="string", example="Benefício de refeição para funcionários"),
 *     @OA\Property(property="valor_maximo", type="number", format="float", example=500),
 *     @OA\Property(property="ativo", type="boolean", example=true),
 *     @OA\Property(property="criado_em", type="string", example="2025-09-02 23:01:05"),
 *     @OA\Property(property="atualizado_em", type="string", example="2025-09-02 23:01:05")
 * )
 */
class Beneficio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'beneficios';

    protected $fillable = [
        'nome',
        'tipo',
        'descricao',
        'valor_maximo',
        'regras',
        'ativo',
        'requer_aprovacao_dupla'
    ];

    protected $casts = [
        'tipo' => TipoBeneficio::class,
        'valor_maximo' => 'decimal:2',
        'regras' => 'array',
        'ativo' => 'boolean',
        'requer_aprovacao_dupla' => 'boolean'
    ];

    public function solicitacoes()
    {
        return $this->hasMany(SolicitacaoBeneficio::class, 'beneficio_id');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, TipoBeneficio $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}

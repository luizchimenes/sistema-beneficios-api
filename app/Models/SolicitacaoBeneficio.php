<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusSolicitacao;

/**
 * @OA\Schema(
 *     schema="SolicitacaoBeneficio",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="beneficio_id", type="integer", example=2),
 *     @OA\Property(property="usuario_id", type="integer", example=5),
 *     @OA\Property(property="valor_solicitado", type="number", format="float", example=150.50),
 *     @OA\Property(property="justificativa", type="string", example="Preciso deste benefício para alimentação"),
 *     @OA\Property(property="status", type="string", example="pendente"),
 *     @OA\Property(property="criado_em", type="string", example="2025-09-02 23:01:05"),
 *     @OA\Property(property="atualizado_em", type="string", example="2025-09-02 23:01:05")
 * )
 */
class SolicitacaoBeneficio extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes_beneficios';

    protected $fillable = [
        'usuario_id',
        'beneficio_id',
        'valor_solicitado',
        'justificativa',
        'status',
        'aprovado_por',
        'aprovado_em',
        'segunda_aprovacao_por',
        'segunda_aprovacao_em',
        'motivo_rejeicao'
    ];

    protected $casts = [
        'status' => StatusSolicitacao::class,
        'valor_solicitado' => 'decimal:2',
        'aprovado_em' => 'datetime',
        'segunda_aprovacao_em' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function beneficio()
    {
        return $this->belongsTo(Beneficio::class, 'beneficio_id');
    }

    public function aprovador()
    {
        return $this->belongsTo(Usuario::class, 'aprovado_por');
    }

    public function segundoAprovador()
    {
        return $this->belongsTo(Usuario::class, 'segunda_aprovacao_por');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', StatusSolicitacao::PENDENTE);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusSolicitacao;

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
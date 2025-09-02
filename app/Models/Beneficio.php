<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TipoBeneficio;

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
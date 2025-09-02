<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Enums\TipoUsuario;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'tipo',
        'departamento',
        'ativo'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'tipo' => TipoUsuario::class,
        'ativo' => 'boolean',
        'email_verificado_em' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function solicitacoesBeneficios()
    {
        return $this->hasMany(SolicitacaoBeneficio::class, 'usuario_id');
    }

    public function aprovacoes()
    {
        return $this->hasMany(SolicitacaoBeneficio::class, 'aprovado_por');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'tipo' => $this->tipo->value,
            'departamento' => $this->departamento
        ];
    }

    public function podeAprovar(): bool
    {
        return in_array($this->tipo, [TipoUsuario::APROVADOR, TipoUsuario::ADMIN]);
    }

    public function ehAdmin(): bool
    {
        return $this->tipo === TipoUsuario::ADMIN;
    }
}
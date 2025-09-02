<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusSolicitacao;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_beneficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('beneficio_id')->constrained('beneficios');
            $table->decimal('valor_solicitado', 10, 2);
            $table->text('justificativa')->nullable();
            $table->enum('status', array_column(StatusSolicitacao::cases(), 'value'))
                  ->default(StatusSolicitacao::PENDENTE->value);
            $table->foreignId('aprovado_por')->nullable()->constrained('usuarios');
            $table->timestamp('aprovado_em')->nullable();
            $table->foreignId('segunda_aprovacao_por')->nullable()->constrained('usuarios');
            $table->timestamp('segunda_aprovacao_em')->nullable();
            $table->text('motivo_rejeicao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_beneficios');
    }
};